<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use ZipArchive;

class BiometricController extends Controller
{
    /**
     * Show the biometric enrollment page for a member.
     */
    public function showEnrollment(Member $member)
    {
        return view('admin.members.biometric', compact('member'));
    }

    /**
     * Store a fingerprint template captured by the scanner.
     * Expects JSON: { fingerprint_template: "<base64>", finger_index: 1|2 }
     */
    public function enroll(Request $request, Member $member)
    {
        $request->validate([
            'fingerprint_template' => 'required|string',
            'finger_index'         => 'required|in:1,2',
        ]);

        $template     = $request->input('fingerprint_template');
        $fingerIndex  = (int) $request->input('finger_index');

        $templateHash = hash('sha256', $template);
        $hashField    = $fingerIndex === 1 ? 'fingerprint_hash_1' : 'fingerprint_hash_2';
        $tmplField    = $fingerIndex === 1 ? 'fingerprint_template_1' : 'fingerprint_template_2';

        // Server-side duplicate guard: if this exact template is already stored for
        // another member, reject immediately (catches copy-paste or session replay).
        $conflict = Member::where($hashField, $templateHash)
            ->where('id', '!=', $member->id)
            ->first();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'This fingerprint is already enrolled for ' . $conflict->full_name
                           . '. Each member must use their own unique finger.',
            ], 422);
        }

        $member->update([
            $tmplField              => $template,
            $hashField              => $templateHash,
            'biometric_enrolled'    => true,
            'biometric_enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint enrolled successfully',
            'finger'  => $fingerIndex === 1 ? 'Primary' : 'Backup',
        ]);
    }

    /**
     * Return ALL enrolled members' templates — used by the create-member page
     * where no member ID exists yet to exclude.
     */
    public function allEnrolledTemplates(): JsonResponse
    {
        $members = Member::where('biometric_enrolled', true)
            ->whereNotNull('fingerprint_template_1')
            ->select('id', 'first_name', 'last_name',
                     'fingerprint_template_1', 'fingerprint_template_2')
            ->get()
            ->map(fn($m) => [
                'id'   => $m->id,
                'name' => $m->first_name . ' ' . $m->last_name,
                't1'   => $m->fingerprint_template_1,
                't2'   => $m->fingerprint_template_2,
            ])
            ->values();

        return response()->json(['members' => $members]);
    }

    /**
     * Return all OTHER enrolled members' decrypted templates for duplicate checking.
     * Used by the enrollment page to detect if a finger is already registered.
     */
    public function enrolledTemplates(Member $member): JsonResponse
    {
        $members = Member::where('biometric_enrolled', true)
            ->whereNotNull('fingerprint_template_1')
            ->where('id', '!=', $member->id)
            ->select('id', 'first_name', 'last_name',
                     'fingerprint_template_1', 'fingerprint_template_2')
            ->get()
            ->map(fn($m) => [
                'id'   => $m->id,
                'name' => $m->first_name . ' ' . $m->last_name,
                't1'   => $m->fingerprint_template_1,
                't2'   => $m->fingerprint_template_2,
            ])
            ->values();

        return response()->json(['members' => $members]);
    }

    /**
     * Package the ZKFinger bridge installer as a zip and stream it for download.
     * Admins download this once and run install-service.bat on each scanner PC.
     */
    public function downloadBridge()
    {
        $bridgeDir = base_path('zkfinger-bridge');

        $files = [
            'bin/Release/net48/ZKFingerBridge.exe' => 'ZKFingerBridge/ZKFingerBridge.exe',
            'bin/Release/net48/libzkfp.dll'        => 'ZKFingerBridge/libzkfp.dll',
            'bridge-wrapper.ps1'                   => 'ZKFingerBridge/bridge-wrapper.ps1',
            'install-service.bat'                  => 'ZKFingerBridge/install-service.bat',
            'uninstall-service.bat'                => 'ZKFingerBridge/uninstall-service.bat',
        ];

        // Verify at least the installer scripts exist
        $scriptsExist = file_exists($bridgeDir . DIRECTORY_SEPARATOR . 'install-service.bat');
        if (!$scriptsExist) {
            abort(404, 'Bridge files not found. Expected path: ' . $bridgeDir);
        }

        $tmpZip = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'zkbridge_' . uniqid() . '.zip';
        $zip    = new ZipArchive();

        $opened = $zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($opened !== true) {
            abort(500, 'Could not create zip archive (ZipArchive error ' . $opened . ').');
        }

        $added = 0;
        foreach ($files as $relative => $zipPath) {
            $full = $bridgeDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (file_exists($full)) {
                $zip->addFile($full, $zipPath);
                $added++;
            }
        }

        $zip->close();

        if (!file_exists($tmpZip)) {
            abort(500, 'Zip file was not created. ' . $added . ' file(s) were found.');
        }

        return response()->download($tmpZip, 'ZKFingerBridge-Setup.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Remove all biometric data for a member.
     */
    public function remove(Member $member)
    {
        $member->update([
            'fingerprint_template_1' => null,
            'fingerprint_template_2' => null,
            'biometric_enrolled'     => false,
            'biometric_enrolled_at'  => null,
        ]);

        return back()->with('success', 'Biometric data removed successfully.');
    }
}
