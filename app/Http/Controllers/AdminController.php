<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::with('roles')->latest()->paginate(5);
        $roles = Role::where('is_active', true)->get();
        return view('admin.admins.index', compact('admins', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'phone' => 'nullable|string|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['roles'])) {
            $admin->roles()->attach($validated['roles']);
    }

        // If it's an AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully.',
                'admin' => $admin->load('roles')
            ], 201);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    public function show(Admin $admin)
    {
        $admin->load('roles');
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        $roles = Role::where('is_active', true)->get();
        $admin->load('roles');

        // If it's an AJAX request, return JSON for modal
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'admin' => $admin,
                'roles' => $roles,
                'adminRoles' => $admin->roles->pluck('id')->toArray(),
            ]);
        }

        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:255|unique:admins,phone,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $admin->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['roles'])) {
            $admin->roles()->sync($validated['roles']);
        } else {
            $admin->roles()->detach();
        }

        // If it's an AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully.',
                'admin' => $admin->fresh()->load('roles')
            ]);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->is_super_admin) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Cannot delete super admin.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }
}
