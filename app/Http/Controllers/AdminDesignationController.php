<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\User;
use App\Services\MemberIdCardService;
use Illuminate\Http\Request;

class AdminDesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withCount('users')
            ->get();

        return view('admin.designations.index', compact('designations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
            'sort_order' => 'nullable|integer|min:0|max:65535',
        ]);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        Designation::create($validated);

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation created.');
    }

    public function edit(Designation $designation)
    {
        return view('admin.designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'sort_order' => 'nullable|integer|min:0|max:65535',
        ]);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $designation->update($validated);

        // Regenerate ID cards for all members holding this designation
        $users = User::where('designation_id', $designation->id)->get();
        $cardService = app(MemberIdCardService::class);
        foreach ($users as $user) {
            $user->setRelation('designation', $designation);
            $user->load(['activeSubscription.plan', 'subscriptions']);
            $cardService->ensureCardGenerated($user, force: true);
        }

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation updated and member ID cards refreshed.');
    }

    public function destroy(Designation $designation)
    {
        $userIds = User::where('designation_id', $designation->id)->pluck('id');

        $designation->delete();

        if ($userIds->isNotEmpty()) {
            $users = User::whereIn('id', $userIds)->get();
            $cardService = app(MemberIdCardService::class);
            foreach ($users as $user) {
                $user->unsetRelation('designation');
                $user->load(['activeSubscription.plan', 'subscriptions']);
                $cardService->ensureCardGenerated($user, force: true);
            }
        }

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation removed. Members with this designation were cleared and ID cards refreshed.');
    }
}
