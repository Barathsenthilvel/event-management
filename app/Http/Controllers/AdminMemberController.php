<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\User;
use App\Services\MembershipLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminMemberController extends Controller
{
    public function __construct(
        private readonly MembershipLifecycleService $membershipLifecycle,
    ) {}

    public function show(Request $request, User $user)
    {
        $tab = (string) $request->query('tab', 'all');
        $q = trim((string) $request->query('q', ''));

        $this->membershipLifecycle->syncUser($user);

        $query = ['tab' => $tab];
        if ($q !== '') {
            $query['q'] = $q;
        }

        return view('admin.members.pending-approval-show', [
            'member' => $user->load([
                'designation',
                'activeSubscription.plan',
                'paymentTransactions' => fn ($q) => $q->orderByDesc('id')->limit(5),
            ]),
            'showApprovalActions' => false,
            'backUrl' => route('admin.members.index', $query),
            'backLabel' => 'Back to members list',
            'inactiveTypeOptions' => MembershipLifecycleService::inactiveTypeOptions(),
        ]);
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $q = trim((string) $request->query('q', ''));

        $base = User::query()->orderByDesc('id');

        $totalCount = (clone $base)->count();
        $activeCount = (clone $base)
            ->where('profile_completed', true)
            ->where('is_approved', true)
            ->whereIn('membership_status', [
                MembershipLifecycleService::STATUS_ACTIVE,
                MembershipLifecycleService::STATUS_GRACE,
            ])
            ->count();
        $inactiveCount = (clone $base)
            ->where(function ($query) {
                $query->where('profile_completed', false)
                    ->orWhere('is_approved', false)
                    ->orWhere('membership_status', MembershipLifecycleService::STATUS_INACTIVE);
            })
            ->count();

        $members = $base
            ->with([
                'designation',
                'activeSubscription.plan',
                'paymentTransactions' => fn ($q) => $q->orderByDesc('id')->limit(5),
            ])
            ->when($tab === 'active', function ($query) {
                $query->where('profile_completed', true)
                    ->where('is_approved', true)
                    ->whereIn('membership_status', [
                        MembershipLifecycleService::STATUS_ACTIVE,
                        MembershipLifecycleService::STATUS_GRACE,
                    ]);
            })
            ->when($tab === 'inactive', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('profile_completed', false)
                        ->orWhere('is_approved', false)
                        ->orWhere('membership_status', MembershipLifecycleService::STATUS_INACTIVE);
                });
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%");
                });
            })
            ->paginate(20)
            ->withQueryString();

        $designations = Designation::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.members.index', [
            'members' => $members,
            'designations' => $designations,
            'tab' => $tab,
            'q' => $q,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'inactiveTypeOptions' => MembershipLifecycleService::inactiveTypeOptions(),
        ]);
    }

    public function updateDesignation(Request $request, User $user)
    {
        $request->merge([
            'designation_id' => $request->filled('designation_id') ? $request->input('designation_id') : null,
        ]);

        $validated = $request->validate([
            'designation_id' => 'nullable|exists:designations,id',
        ]);

        $user->designation_id = $validated['designation_id'];
        $user->save();

        return redirect()
            ->back()
            ->with('success', 'Member designation updated.');
    }

    public function removed(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $members = User::onlyTrashed()
            ->with(['designation'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('deleted_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.members.removed', [
            'members' => $members,
            'q' => $q,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Member removed. You can restore them from the Removed Members list.');
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()
            ->route('admin.members.removed')
            ->with('success', 'Member restored successfully.');
    }

    public function updateMembershipStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['mark_inactive', 'clear_inactive'])],
            'membership_inactive_type' => [
                'required_if:action,mark_inactive',
                'nullable',
                Rule::in(array_keys(MembershipLifecycleService::inactiveTypeOptions())),
            ],
        ]);

        if ($validated['action'] === 'mark_inactive') {
            $this->membershipLifecycle->markInactiveByAdmin(
                $user,
                (string) $validated['membership_inactive_type'],
            );

            return redirect()
                ->back()
                ->with('success', 'Member marked as membership inactive.');
        }

        $this->membershipLifecycle->markActiveByAdmin($user);

        return redirect()
            ->back()
            ->with('success', 'Membership inactive status cleared.');
    }
}
