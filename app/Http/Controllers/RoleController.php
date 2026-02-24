<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(5);

        // If it's an AJAX request, return JSON (for table-only refresh after create/update)
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'roles' => $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'description' => $role->description,
                        'is_active' => $role->is_active,
                        'created_at' => $role->created_at->format('d M Y'),
                        'initial' => strtoupper(substr($role->name, 0, 1)),
                    ];
                }),
                'pagination' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'total' => $roles->total(),
                    'first_item' => $roles->firstItem(),
                    'last_item' => $roles->lastItem(),
                    'has_pages' => $roles->hasPages(),
                    'links_html' => (string) $roles->links('pagination.simple-tailwind'),
                ]
            ]);
        }

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        // If it's an AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'role' => $role
            ], 201);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'admins');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');
        $role->load('permissions');

        // If it's an AJAX request, return JSON for modal
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $role->permissions->pluck('slug')->toArray(),
            ]);
        }

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : false,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        // If it's an AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'role' => $role->fresh()
            ]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function getPermissionsData(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions->pluck('slug')->toArray(),
        ]);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
        ]);

        // Get permission IDs by slug
        $permissionSlugs = $validated['permissions'] ?? [];
        $permissions = Permission::whereIn('slug', $permissionSlugs)->pluck('id');

        $role->permissions()->sync($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Permissions updated successfully for ' . $role->name);
    }
}
