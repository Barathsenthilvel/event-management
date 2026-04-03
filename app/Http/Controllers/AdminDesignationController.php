<?php

namespace App\Http\Controllers;

use App\Models\Designation;
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

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation removed. Members with this designation were cleared.');
    }
}
