<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')->orderBy('parent_id')->orderBy('order')->get();
        $parents = Menu::whereNull('parent_id')->orderBy('order')->get();
        $menuRoutes = $this->adminMenuRoutes();

        return view('admin.menus.index', compact('menus', 'parents', 'menuRoutes'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedMenuPayload($request);

        Menu::create($data);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu created successfully.');
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validatedMenuPayload($request);

        if ($data['parent_id'] !== null && (int) $data['parent_id'] === (int) $menu->id) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['parent_id' => 'A menu cannot be its own parent.']);
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedMenuPayload(Request $request): array
    {
        $orderRaw = $request->input('order');
        if ($orderRaw === null || $orderRaw === '') {
            $request->merge(['order' => 0]);
        }

        $isRoot = in_array($request->input('is_root_menu'), [1, '1', true, 'true'], true);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'route_name' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_root_menu' => ['required', Rule::in([0, 1, '0', '1'])],
            'parent_id' => [
                Rule::requiredIf(! $isRoot),
                'nullable',
                'exists:menus,id',
            ],
            'order' => ['required', 'integer', 'min:0'],
        ]);

        $data['parent_id'] = $isRoot ? null : (isset($data['parent_id']) ? (int) $data['parent_id'] : null);
        $data['order'] = (int) $data['order'];
        $data['is_active'] = $request->boolean('is_active');

        unset($data['is_root_menu']);

        return $data;
    }

    /**
     * Admin route names suitable for sidebar menu links (index/list pages).
     *
     * @return array<int, array{name: string, label: string}>
     */
    private function adminMenuRoutes(): array
    {
        $labels = [
            'admin.dashboard' => 'Dashboard',
            'admin.notification-batches.index' => 'Notification Logs',
            'admin.admins.index' => 'Users (Admins)',
            'admin.roles.index' => 'Roles & Permissions',
            'admin.menus.index' => 'Menu Management',
            'admin.home-banners.index' => 'Home Banners',
            'admin.home-blogs.index' => 'Home Blogs',
            'admin.home-galleries.index' => 'Home Galleries',
            'admin.ebooks.index' => 'E-Books',
            'admin.memberships.index' => 'Membership Plans',
            'admin.members.pending-approvals.index' => 'Member Approvals',
            'admin.members.index' => 'Members',
            'admin.designations.index' => 'Designations',
            'admin.subscriptions.index' => 'Subscriptions',
            'admin.events.index' => 'Events',
            'admin.donations.index' => 'Donations',
            'admin.donations.payments.index' => 'Donation Payments',
            'admin.meetings.index' => 'Meetings',
            'admin.jobs.index' => 'Jobs',
            'admin.jobs.need-job.requests' => 'Need Job Requests',
            'admin.nominations.index' => 'Nominations',
            'admin.pollings.index' => 'Pollings',
        ];

        return collect($labels)
            ->filter(fn (string $label, string $name) => Route::has($name))
            ->map(fn (string $label, string $name) => ['name' => $name, 'label' => $label])
            ->values()
            ->all();
    }
}

