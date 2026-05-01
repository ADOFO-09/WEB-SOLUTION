<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmsMessage;
use App\Models\SmsTemplate;
use App\Models\SmsRecipient;
use App\Models\Member;
use App\Models\Ministry;
use App\Services\GiantSmsService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;

class SmsController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:sms.view', only: ['index', 'show']),
            new Middleware('permission:sms.send', only: ['compose', 'store', 'send']),
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
        $messages = $query->paginate(20)->withQueryString();

        $stats = [
            'total_sent' => SmsMessage::whereIn('status', ['sent', 'partially_sent'])->count(),
            'total_recipients' => SmsMessage::sum('recipient_count'),
            'total_delivered' => SmsMessage::sum('successful_count'),
            'this_month' => SmsMessage::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'drafts' => SmsMessage::where('status', 'draft')->count(),
        ];

        return view('admin.sms.index', compact('messages', 'stats'));
    }

    public function show(SmsMessage $smsMessage)
    {
        $smsMessage->load(['sentBy', 'recipients' => fn($q) => $q->orderBy('status')->limit(100)]);
        
        $recipientStats = [
            'pending' => $smsMessage->recipients()->where('status', 'pending')->count(),
            'sent' => $smsMessage->recipients()->where('status', 'sent')->count(),
            'delivered' => $smsMessage->recipients()->where('status', 'delivered')->count(),
            'failed' => $smsMessage->recipients()->where('status', 'failed')->count(),
        ];

        return view('admin.sms.show', compact('smsMessage', 'recipientStats'));
    }

    public function compose(Request $request)
    {
        $templates = SmsTemplate::where('is_active', true)->orderBy('name')->get();
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
        $memberCount = Member::where('membership_status', 'active')->whereNotNull('phone_primary')->count();
        
        $selectedTemplate = $request->has('template_id') 
            ? SmsTemplate::find($request->template_id) 
            : null;

        return view('admin.sms.compose', compact('templates', 'ministries', 'memberCount', 'selectedTemplate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'message_content' => 'required|string|max:480',
            'category' => 'required|in:general,financial,attendance,event,reminder,birthday',
            'recipient_type' => 'required|in:all,ministry,custom',
            'ministry_id' => 'required_if:recipient_type,ministry|nullable|exists:ministries,id',
            'custom_numbers' => 'required_if:recipient_type,custom|nullable|string',
        ]);

        // Create the message
        $message = SmsMessage::create([
            'message_type' => 'bulk',
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message_content' => $validated['message_content'],
            'status' => 'draft',
            'sent_by' => auth()->id(),
        ]);

        // Add recipients
        $recipients = $this->resolveRecipients($validated);
        
        foreach ($recipients as $recipient) {
            $message->recipients()->create([
                'member_id' => $recipient['member_id'] ?? null,
                'phone' => $recipient['phone'],
                'recipient_name' => $recipient['name'] ?? null,
                'status' => 'pending',
            ]);
        }

        $message->update(['recipient_count' => $message->recipients()->count()]);

        if ($request->input('action') === 'send') {
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
        return view('admin.sms.templates.create');
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:general,financial,attendance,event,reminder,birthday',
            'content' => 'required|string|max:480',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        // Extract variables from content
        preg_match_all('/\{(\w+)\}/', $validated['content'], $matches);
        $validated['variables'] = $matches[1] ?? [];

        SmsTemplate::create($validated);

        return redirect()->route('admin.sms.templates')
            ->with('success', 'Template created successfully.');
    }

    public function editTemplate(SmsTemplate $smsTemplate)
    {
        return view('admin.sms.templates.edit', ['smsTemplate' => $smsTemplate]);
    }

    public function updateTemplate(Request $request, SmsTemplate $smsTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:general,financial,attendance,event,reminder,birthday',
            'content' => 'required|string|max:480',
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

    private function resolveRecipients(array $data): array
    {
        $recipients = [];

        switch ($data['recipient_type']) {
            case 'all':
                $members = Member::where('membership_status', 'active')->whereNotNull('phone_primary')->get();
                foreach ($members as $member) {
                    $recipients[] = [
                        'member_id' => $member->id,
                        'phone' => $member->phone_primary,
                        'name' => $member->full_name,
                    ];
                }
                break;

            case 'ministry':
                $ministry = Ministry::find($data['ministry_id']);
                if ($ministry) {
                    $members = $ministry->members()
                        ->whereNotNull('phone_primary')
                        ->where('membership_status', 'active')
                        ->get();
                    foreach ($members as $member) {
                        $recipients[] = [
                            'member_id' => $member->id,
                            'phone' => $member->phone_primary,
                            'name' => $member->full_name,
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
                            'phone' => $number,
                            'name' => $member?->full_name ?? 'Unknown',
                        ];
                    }
                }
                break;
        }

        return $recipients;
    }

    private function sendMessage(SmsMessage $message): void
    {
        $message->update([
            'status' => 'sending',
            'sent_at' => now(),
        ]);

        $successCount = 0;
        $failCount = 0;

        $provider   = Setting::get('sms_provider', '');
        $smsService = $provider === 'giantsms' ? new GiantSmsService() : null;

        foreach ($message->recipients()->where('status', 'pending')->get() as $recipient) {
            try {
                if ($smsService) {
                    $result = $smsService->send($recipient->phone_number, $message->message_content);
                    $recipient->update([
                        'status'             => 'sent',
                        'sent_at'            => now(),
                        'gateway_message_id' => $result['message_id'] ?? null,
                    ]);
                } else {
                    $recipient->update(['status' => 'sent', 'sent_at' => now()]);
                }
                $successCount++;
            } catch (\Exception $e) {
                $recipient->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $failCount++;
            }
        }

        $message->update([
            'successful_count' => $successCount,
            'failed_count' => $failCount,
            'status' => $failCount === 0 ? 'sent' : ($successCount === 0 ? 'failed' : 'partially_sent'),
        ]);
    }
}
