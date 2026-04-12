<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RoleMenuPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount(['permissions', 'menuPermissions'])
            ->latest()
            ->paginate(10);

        // If it's an AJAX request, return JSON (for table-only refresh after create/update)
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'roles' => $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'description' => $role->description,
                        'is_active' => (bool) $role->is_active,
                        'permissions_count' => (int) $role->permissions_count,
                        'menu_permissions_count' => (int) $role->menu_permissions_count,
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

        $canManageMenuPermissions = auth()->guard('admin')->check()
            && auth()->guard('admin')->user()->is_super_admin;

        return view('admin.roles.index', compact('roles', 'canManageMenuPermissions'));
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
        $admin = auth()->guard('admin')->user();
        if (! $admin || ! $admin->is_super_admin) {
            abort(403, 'Only Super Admin can manage menu permissions.');
        }

        $menus = Menu::orderBy('order')->orderBy('title')->get(['id', 'title']);
        $menuPermissions = $role->menuPermissions()->get()->keyBy('menu_id');

        $menuPermissionsMap = [];
        foreach ($menus as $menu) {
            $p = $menuPermissions->get($menu->id);
            $menuPermissionsMap[$menu->id] = [
                'can_view' => $p ? $p->can_view : false,
                'can_create' => $p ? $p->can_create : false,
                'can_edit' => $p ? $p->can_edit : false,
                'can_delete' => $p ? $p->can_delete : false,
            ];
        }

        return response()->json([
            'menus' => $menus->map(fn ($m) => ['id' => $m->id, 'title' => $m->title]),
            'menuPermissions' => $menuPermissionsMap,
        ]);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin || ! $admin->is_super_admin) {
            abort(403, 'Only Super Admin can manage menu permissions.');
        }

        $validated = $request->validate([
            'menu_permissions' => 'nullable|array',
            'menu_permissions.*.menu_id' => 'required|exists:menus,id',
            'menu_permissions.*.can_view' => 'nullable|boolean',
            'menu_permissions.*.can_create' => 'nullable|boolean',
            'menu_permissions.*.can_edit' => 'nullable|boolean',
            'menu_permissions.*.can_delete' => 'nullable|boolean',
        ]);

        $role->menuPermissions()->delete();

        foreach ($validated['menu_permissions'] ?? [] as $row) {
            $view = filter_var($row['can_view'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $create = filter_var($row['can_create'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $edit = filter_var($row['can_edit'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $delete = filter_var($row['can_delete'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($view || $create || $edit || $delete) {
                RoleMenuPermission::create([
                    'role_id' => $role->id,
                    'menu_id' => $row['menu_id'],
                    'can_view' => $view,
                    'can_create' => $create,
                    'can_edit' => $edit,
                    'can_delete' => $delete,
                ]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu permissions updated successfully for ' . $role->name,
            ]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Menu permissions updated successfully for ' . $role->name);
    }
}
