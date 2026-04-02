<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')->orderBy('parent_id')->orderBy('order')->get();
        $parents = Menu::whereNull('parent_id')->orderBy('order')->get();

        return view('admin.menus.index', compact('menus', 'parents'));
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
}

