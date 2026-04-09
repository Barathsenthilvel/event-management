<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all'); // all | active | inactive
        $q = trim((string) $request->query('q', ''));

        $base = User::query()->orderByDesc('id');

        $totalCount = (clone $base)->count();
        $activeCount = (clone $base)
            ->where('profile_completed', true)
            ->where('is_approved', true)
            ->count();
        $inactiveCount = $totalCount - $activeCount;

        $members = $base
            ->with([
                'designation',
                'activeSubscription.plan',
                'paymentTransactions' => fn ($q) => $q->orderByDesc('id')->limit(5),
            ])
            ->when($tab === 'active', function ($query) {
                $query->where('profile_completed', true)
                    ->where('is_approved', true);
            })
            ->when($tab === 'inactive', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('profile_completed', false)
                        ->orWhere('is_approved', false);
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
}

