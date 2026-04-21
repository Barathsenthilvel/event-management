<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            if (!empty($user->email)) {
                $paymentUrl = route('member.subscription.index');
                $memberName = $user->name ?: trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
                Mail::raw(
                    "Hello {$memberName},\n\nYour GNAT member profile has been approved.\n\nPlease complete your membership payment to activate access:\n{$paymentUrl}\n\nThank you,\nGNAT Team",
                    function ($message) use ($user) {
                        $message->to($user->email)->subject('Profile approved - complete membership payment');
                    }
                );
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

        return redirect()
            ->route('admin.members.pending-approvals.index')
            ->with('success', 'Member marked as not approved.');
    }
}

