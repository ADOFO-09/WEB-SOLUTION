<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmsMessage;
use App\Models\SmsTemplate;
use App\Models\Member;
use App\Models\Ministry;
use App\Services\GiantSmsService;
use App\Services\PlaceholderService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use App\Helpers\SettingHelper;

class SmsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:sms.view',      only: ['index', 'show']),
            new Middleware('permission:sms.send',      only: ['compose', 'store', 'send']),
            new Middleware('permission:sms.templates', only: ['templates', 'createTemplate', 'storeTemplate', 'editTemplate', 'updateTemplate', 'destroyTemplate']),
        ];
    }

    // ==========================================
    // SMS MESSAGES
    // ==========================================

    public function index(Request $request)
    {
        $query = SmsMessage::with('sentBy');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('message_content', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $query->orderBy('created_at', 'desc');
        $messages = $query->paginate(SettingHelper::perPage())->withQueryString();

        $stats = [
            'total_sent'       => SmsMessage::whereIn('status', ['sent', 'partially_sent'])->count(),
            'total_recipients' => SmsMessage::sum('recipient_count'),
            'total_delivered'  => SmsMessage::sum('successful_count'),
            'this_month'       => SmsMessage::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'drafts'           => SmsMessage::where('status', 'draft')->count(),
        ];

        $balanceAlert = $this->buildBalanceAlert();

        return view('admin.sms.index', compact('messages', 'stats', 'balanceAlert'));
    }

    public function show(SmsMessage $smsMessage)
    {
        $smsMessage->load(['sentBy', 'recipients' => fn($q) => $q->orderBy('status')->limit(100)]);

        $recipientStats = [
            'pending'   => $smsMessage->recipients()->where('status', 'pending')->count(),
            'sent'      => $smsMessage->recipients()->where('status', 'sent')->count(),
            'delivered' => $smsMessage->recipients()->where('status', 'delivered')->count(),
            'failed'    => $smsMessage->recipients()->where('status', 'failed')->count(),
        ];

        return view('admin.sms.show', compact('smsMessage', 'recipientStats'));
    }

    public function compose(Request $request)
    {
        $templates   = SmsTemplate::where('is_active', true)->orderBy('name')->get();
        $ministries  = Ministry::where('is_active', true)->orderBy('name')->get();
        $memberCount = Member::where('membership_status', 'active')->whereNotNull('phone_primary')->count();

        $selectedTemplate = $request->has('template_id')
            ? SmsTemplate::find($request->template_id)
            : null;

        $balanceAlert = $this->buildBalanceAlert();

        // Build registry + live system preview values for the compose UI
        $svc          = new PlaceholderService();
        $uiRegistry   = $svc->uiRegistry();
        $systemPreview = $svc->resolveSystemValues();

        return view('admin.sms.compose', compact(
            'templates', 'ministries', 'memberCount',
            'selectedTemplate', 'balanceAlert',
            'uiRegistry', 'systemPreview'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'          => 'nullable|string|max:255',
            'message_content'  => 'required|string|max:320',
            'category'         => 'required|in:general,financial,attendance,event,reminder,birthday',
            'recipient_type'   => 'required|in:all,ministry,custom',
            'ministry_id'      => 'required_if:recipient_type,ministry|nullable|exists:ministries,id',
            'custom_numbers'   => 'required_if:recipient_type,custom|nullable|string',
        ]);

        $manualValues = $request->input('placeholders', []);
        $isSending    = $request->input('action') === 'send';

        // Block send when unknown placeholders are present
        if ($isSending) {
            $svc      = new PlaceholderService();
            $problems = array_filter(
                $svc->validate($validated['message_content'], $manualValues),
                fn($w) => $w['level'] === 'error'
            );
            if (!empty($problems)) {
                $messages = array_column(array_values($problems), 'message');
                return back()
                    ->withErrors(['message_content' => $messages])
                    ->withInput();
            }
        }

        $message = SmsMessage::create([
            'message_type'               => 'bulk',
            'category'                   => $validated['category'],
            'subject'                    => $validated['subject'],
            'message_content'            => $validated['message_content'],
            'manual_placeholder_values'  => $manualValues ?: null,
            'status'                     => 'draft',
            'sent_by'                    => auth()->user()?->id,
        ]);

        $recipients = $this->resolveRecipients($validated);

        foreach ($recipients as $recipient) {
            $message->recipients()->create([
                'member_id'      => $recipient['member_id'] ?? null,
                'phone_number'   => $recipient['phone'],
                'recipient_name' => $recipient['name'] ?? null,
                'status'         => 'pending',
            ]);
        }

        $message->update(['recipient_count' => $message->recipients()->count()]);

        if ($isSending) {
            $this->sendMessage($message);
            return redirect()->route('admin.sms.show', $message)
                ->with('success', "Message sent to {$message->recipient_count} recipients.");
        }

        return redirect()->route('admin.sms.show', $message)
            ->with('success', 'Message saved as draft with ' . $message->recipient_count . ' recipients.');
    }

    public function send(SmsMessage $smsMessage)
    {
        if (!in_array($smsMessage->status, ['draft', 'failed'])) {
            return back()->with('error', 'This message cannot be sent.');
        }

        $this->sendMessage($smsMessage);

        return back()->with('success', "Message sent! {$smsMessage->successful_count} delivered, {$smsMessage->failed_count} failed.");
    }

    public function destroy(SmsMessage $smsMessage)
    {
        if ($smsMessage->status !== 'draft') {
            return back()->with('error', 'Only draft messages can be deleted.');
        }

        $smsMessage->recipients()->delete();
        $smsMessage->delete();

        return redirect()->route('admin.sms.index')
            ->with('success', 'Draft message deleted.');
    }

    // ==========================================
    // SMS TEMPLATES
    // ==========================================

    public function templates()
    {
        $templates = SmsTemplate::with('createdBy')->orderBy('category')->orderBy('name')->get();
        return view('admin.sms.templates.index', compact('templates'));
    }

    public function createTemplate()
    {
        $uiRegistry = (new PlaceholderService())->uiRegistry();
        return view('admin.sms.templates.create', compact('uiRegistry'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'category'  => 'required|in:general,financial,attendance,event,reminder,birthday',
            'content'   => 'required|string|max:480',
            'is_active' => 'boolean',
        ]);

        $validated['is_active']   = $request->boolean('is_active', true);
        $validated['created_by']  = auth()->user()?->id;

        preg_match_all('/\{(\w+)\}/', $validated['content'], $matches);
        $validated['variables'] = $matches[1] ?? [];

        SmsTemplate::create($validated);

        return redirect()->route('admin.sms.templates')
            ->with('success', 'Template created successfully.');
    }

    public function editTemplate(SmsTemplate $smsTemplate)
    {
        $uiRegistry = (new PlaceholderService())->uiRegistry();
        return view('admin.sms.templates.edit', compact('smsTemplate', 'uiRegistry'));
    }

    public function updateTemplate(Request $request, SmsTemplate $smsTemplate)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'category'  => 'required|in:general,financial,attendance,event,reminder,birthday',
            'content'   => 'required|string|max:480',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        preg_match_all('/\{(\w+)\}/', $validated['content'], $matches);
        $validated['variables'] = $matches[1] ?? [];

        $smsTemplate->update($validated);

        return redirect()->route('admin.sms.templates')
            ->with('success', 'Template updated successfully.');
    }

    public function destroyTemplate(SmsTemplate $smsTemplate)
    {
        $smsTemplate->delete();
        return redirect()->route('admin.sms.templates')
            ->with('success', 'Template deleted.');
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    private function buildBalanceAlert(): ?array
    {
        $lastBalance = Setting::get('sms_last_balance');

        if ($lastBalance === null || $lastBalance === '') {
            return null;
        }

        $balance   = (float) $lastBalance;
        $threshold = (int) Setting::get('sms_balance_threshold', 0);
        $checkedAt = Setting::get('sms_last_balance_at');

        if ($threshold <= 0 || $balance > $threshold) {
            return null;
        }

        return [
            'balance'    => $balance,
            'threshold'  => $threshold,
            'checked_at' => $checkedAt,
            'topup_url'  => Setting::get('sms_topup_url') ?: null,
        ];
    }

    private function resolveRecipients(array $data): array
    {
        $recipients = [];

        switch ($data['recipient_type']) {
            case 'all':
                $members = Member::where('membership_status', 'active')
                    ->whereNotNull('phone_primary')
                    ->get(['id', 'member_id', 'first_name', 'middle_name', 'last_name', 'phone_primary']);
                foreach ($members as $member) {
                    $recipients[] = [
                        'member_id' => $member->id,
                        'phone'     => $member->phone_primary,
                        'name'      => $member->full_name,
                    ];
                }
                break;

            case 'ministry':
                $ministry = Ministry::find($data['ministry_id']);
                if ($ministry) {
                    $members = $ministry->members()
                        ->whereNotNull('phone_primary')
                        ->where('membership_status', 'active')
                        ->get(['members.id', 'member_id', 'first_name', 'middle_name', 'last_name', 'phone_primary']);
                    foreach ($members as $member) {
                        $recipients[] = [
                            'member_id' => $member->id,
                            'phone'     => $member->phone_primary,
                            'name'      => $member->full_name,
                        ];
                    }
                }
                break;

            case 'custom':
                $numbers = preg_split('/[\n,;]+/', $data['custom_numbers'] ?? '');
                foreach ($numbers as $number) {
                    $number = trim($number);
                    if (!empty($number)) {
                        $member = Member::where('phone_primary', $number)->first();
                        $recipients[] = [
                            'member_id' => $member?->id,
                            'phone'     => $number,
                            'name'      => $member?->full_name ?? 'Unknown',
                        ];
                    }
                }
                break;
        }

        return $recipients;
    }

    /**
     * Core send loop — resolves placeholders per recipient before dispatching.
     *
     * Per-recipient (Type A) values differ for each person.
     * Manual (Type B) and system (Type C) values are consistent across the batch.
     * The resolved text is stored on the sms_recipients row for audit purposes.
     */
    private function sendMessage(SmsMessage $message): void
    {
        $message->update([
            'status'  => 'sending',
            'sent_at' => now(),
        ]);

        $successCount = 0;
        $failCount    = 0;

        $provider    = Setting::get('sms_provider', '');
        $smsService  = $provider === 'giantsms' ? new GiantSmsService() : null;
        $svc         = new PlaceholderService();
        $manualValues = $message->manual_placeholder_values ?? [];

        // Eager-load the member relationship to avoid N+1 queries
        $pendingRecipients = $message->recipients()
            ->where('status', 'pending')
            ->with('member')
            ->get();

        foreach ($pendingRecipients as $recipient) {
            // Resolve this recipient's personalised message
            $resolved = $svc->resolve(
                $message->message_content,
                $recipient->member,
                $manualValues
            );

            try {
                if ($smsService) {
                    $result = $smsService->send($recipient->phone_number, $resolved);
                    $recipient->update([
                        'status'             => 'sent',
                        'resolved_message'   => $resolved,
                        'sent_at'            => now(),
                        'gateway_message_id' => $result['message_id'] ?? null,
                    ]);
                } else {
                    $recipient->update([
                        'status'           => 'sent',
                        'resolved_message' => $resolved,
                        'sent_at'          => now(),
                    ]);
                }
                $successCount++;
            } catch (\Exception $e) {
                $recipient->update([
                    'status'           => 'failed',
                    'resolved_message' => $resolved,
                    'error_message'    => $e->getMessage(),
                ]);
                $failCount++;
            }
        }

        $message->update([
            'successful_count' => $successCount,
            'failed_count'     => $failCount,
            'status'           => $failCount === 0 ? 'sent' : ($successCount === 0 ? 'failed' : 'partially_sent'),
        ]);

        $this->cacheBalanceAfterSend($smsService);
    }

    private function cacheBalanceAfterSend(?GiantSmsService $smsService): void
    {
        if (!$smsService) {
            return;
        }

        try {
            ['balance' => $balance] = $smsService->getBalance();
            Setting::set('sms_last_balance', (string) $balance, 'sms');
            Setting::set('sms_last_balance_at', now()->toDateTimeString(), 'sms');
        } catch (\Exception) {
            // Non-critical — balance refresh failure should never block a send
        }
    }
}
