<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MemberProfileController extends Controller
{
    private const PENDING_SESSION_KEY = 'member_profile_pending_docs';

    /** @var array<string, array{column: string, rules: list<string>}> */
    private const DOCUMENT_FIELDS = [
        'educational_certificate' => [
            'column' => 'educational_certificate_path',
            'rules' => ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,webp'],
        ],
        'aadhar_card' => [
            'column' => 'aadhar_card_path',
            'rules' => ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,webp'],
        ],
        'passport_photo' => [
            'column' => 'passport_photo_path',
            'rules' => ['file', 'max:5120', 'image', 'mimes:jpg,jpeg,png,webp'],
        ],
    ];

    public function edit()
    {
        $user = Auth::user();

        if ($user->profile_completed || $user->is_approved) {
            session()->forget(self::PENDING_SESSION_KEY);
        }

        $activeSubscription = \App\Models\PaymentTransaction::with('subscriptionPlan')
            ->where('user_id', $user->id)
            ->where('status', 'successful')
            ->latest()
            ->first();

        return view('member.profile.edit', [
            'user' => $user,
            'activeSubscription' => $activeSubscription,
            'pendingProfileDocs' => session(self::PENDING_SESSION_KEY, []),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->profile_completed || $user->is_approved) {
            $message = $user->is_approved
                ? 'Approved profiles cannot be updated. Please contact admin.'
                : 'Your profile was already submitted and cannot be changed. Please contact admin if you need a correction.';

            return redirect()
                ->route('member.profile.edit')
                ->withErrors(['profile' => $message]);
        }

        $pending = $this->stageNewUploads($request, $user);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'digits:10'],
            'dob' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'string', 'max:30'],
            'qualification' => ['required', 'string', 'max:120'],
            'blood_group' => ['required', 'string', 'max:30'],
            'rnrm_number_with_date' => ['required', 'string', 'max:120'],
            'college_name' => ['required', 'string', 'max:190'],
            'door_no' => ['required', 'string', 'max:80'],
            'locality_area' => ['required', 'string', 'max:190'],
            'state' => ['required', 'string', 'max:120'],
            'pin_code' => ['required', 'string', 'max:20'],
            'council_state' => ['required', 'string', 'max:120'],
            'currently_working' => ['nullable', 'string', 'max:190'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        foreach (self::DOCUMENT_FIELDS as $field => $spec) {
            $col = $spec['column'];
            if (!empty($user->$col) || !empty($pending[$field])) {
                continue;
            }

            throw ValidationException::withMessages([
                $field => ['Please upload this document.'],
            ]);
        }

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->mobile = $data['mobile'];
        $user->name = trim($data['first_name'] . ' ' . $data['last_name']);

        $user->dob = $data['dob'];
        $user->gender = $data['gender'];
        $user->qualification = $data['qualification'];
        $user->blood_group = $data['blood_group'];
        $user->rnrm_number_with_date = $data['rnrm_number_with_date'];
        $user->college_name = $data['college_name'];
        $user->door_no = $data['door_no'];
        $user->locality_area = $data['locality_area'];
        $user->state = $data['state'];
        $user->pin_code = $data['pin_code'];
        $user->council_state = $data['council_state'];
        $user->currently_working = $data['currently_working'] ?? null;

        $request->session()->forget(self::PENDING_SESSION_KEY);
        $this->finalizePendingDocuments($user, $pending);

        if (!empty($data['new_password'])) {
            $user->password = $data['new_password'];
        }

        $user->profile_completed = $this->isProfileComplete($user);
        $user->save();

        if ($user->profile_completed && !$user->is_approved) {
            return redirect()
                ->route('member.dashboard')
                ->with('approval_pending_modal', true)
                ->with('success', 'Profile submitted successfully.');
        }

        return redirect()->route('member.dashboard')->with('success', 'Profile updated successfully.');
    }

    /**
     * Store newly uploaded files under pending/ and remember paths in session so a validation
     * error elsewhere does not force the member to re-select documents.
     *
     * @return array<string, string>
     */
    private function stageNewUploads(Request $request, $user): array
    {
        $pendingKey = self::PENDING_SESSION_KEY;
        $pending = $request->session()->get($pendingKey, []);
        $dir = 'member-documents/' . $user->id . '/pending';

        foreach (self::DOCUMENT_FIELDS as $field => $spec) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $request->validate([
                $field => $spec['rules'],
            ]);

            if (!empty($pending[$field])) {
                Storage::disk('public')->delete($pending[$field]);
            }

            $pending[$field] = $request->file($field)->store($dir, 'public');
        }

        $request->session()->put($pendingKey, $pending);

        return $pending;
    }

    /**
     * @param  array<string, string>  $pending
     */
    private function finalizePendingDocuments($user, array $pending): void
    {
        $allowedPrefix = 'member-documents/' . $user->id . '/pending/';
        $baseDir = 'member-documents/' . $user->id;

        foreach (self::DOCUMENT_FIELDS as $field => $spec) {
            if (empty($pending[$field])) {
                continue;
            }

            $pendingPath = $pending[$field];
            if (!str_starts_with($pendingPath, $allowedPrefix)) {
                continue;
            }

            if (!Storage::disk('public')->exists($pendingPath)) {
                continue;
            }

            $extension = pathinfo($pendingPath, PATHINFO_EXTENSION);
            $suffix = $extension !== '' ? '.' . $extension : '';
            $finalRelative = $baseDir . '/' . $field . '_' . bin2hex(random_bytes(8)) . $suffix;

            Storage::disk('public')->move($pendingPath, $finalRelative);

            $col = $spec['column'];
            $old = $user->$col;
            $user->$col = $finalRelative;

            if ($old && $old !== $finalRelative && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }
    }

    private function isProfileComplete($user): bool
    {
        return !empty($user?->first_name)
            && !empty($user?->last_name)
            && !empty($user?->mobile)
            && !empty($user?->dob)
            && !empty($user?->gender)
            && !empty($user?->qualification)
            && !empty($user?->blood_group)
            && !empty($user?->rnrm_number_with_date)
            && !empty($user?->college_name)
            && !empty($user?->door_no)
            && !empty($user?->locality_area)
            && !empty($user?->state)
            && !empty($user?->pin_code)
            && !empty($user?->council_state)
            && !empty($user?->educational_certificate_path)
            && !empty($user?->aadhar_card_path)
            && !empty($user?->passport_photo_path);
    }
}
