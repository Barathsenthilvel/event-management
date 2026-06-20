<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GnatMailService;
use Illuminate\Http\Request;
use Throwable;

class AdminMemberApprovalController extends Controller
{
    public function show(User $user)
    {
        if (!$user->profile_completed || $user->is_approved) {
            abort(404);
        }

        return view('admin.members.pending-approval-show', [
            'member' => $user->load(['designation', 'referrer']),
        ]);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $members = User::query()
            ->where('profile_completed', true)
            ->where('is_approved', false)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('mobile', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.pending-approvals', [
            'members' => $members,
            'q' => $q,
        ]);
    }

    public function approve(User $user)
    {
        $user->is_approved = true;
        $user->save();

        try {
            if (! empty($user->email)) {
                app(GnatMailService::class)->sendProfileApproved($user);
            }
        } catch (Throwable $e) {
            // Approval should succeed even if email provider is unavailable.
        }

        return redirect()
            ->route('admin.members.pending-approvals.index')
            ->with('success', 'Member approved successfully.');
    }

    public function reject(User $user)
    {
        // Keep the record, just mark as not approved.
        $user->is_approved = false;
        $user->save();

        try {
            app(GnatMailService::class)->sendProfileRejected($user);
        } catch (Throwable $e) {
            // Rejection should succeed even if mail/SMS provider is unavailable.
        }

        return redirect()
            ->route('admin.members.pending-approvals.index')
            ->with('success', 'Member marked as not approved.');
    }
}

