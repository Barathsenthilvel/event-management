<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EBookController extends Controller
{
    /**
     * Show the E-Books listing page.
     * For now this uses sample data so the UI works
     * even before the database/model are wired.
     */
    public function index()
    {
        $ebooks = collect([
            [
                'hospital' => 'Hospital A',
                'title' => 'Title 1',
                'code' => 'CODE-001',
                'promote_front' => true,
                'created_on' => '01 Jan 2026',
                'created_by' => 'User name',
                'updated_on' => '02 Jan 2026',
                'updated_by' => 'User name',
                'status' => 'active',
            ],
            [
                'hospital' => 'Hospital B',
                'title' => 'Title 2',
                'code' => 'CODE-002',
                'promote_front' => true,
                'created_on' => '05 Jan 2026',
                'created_by' => 'User name',
                'updated_on' => '06 Jan 2026',
                'updated_by' => 'User name',
                'status' => 'active',
            ],
            [
                'hospital' => 'Hospital C',
                'title' => 'Title 3',
                'code' => 'CODE-003',
                'promote_front' => true,
                'created_on' => '10 Jan 2026',
                'created_by' => 'User name',
                'updated_on' => '11 Jan 2026',
                'updated_by' => 'User name',
                'status' => 'active',
            ],
            [
                'hospital' => 'Hospital D',
                'title' => 'Title 4',
                'code' => 'CODE-004',
                'promote_front' => true,
                'created_on' => '15 Jan 2026',
                'created_by' => 'User name',
                'updated_on' => '16 Jan 2026',
                'updated_by' => 'User name',
                'status' => 'inactive',
            ],
        ]);

        return view('admin.ebooks.index', compact('ebooks'));
    }

    /**
     * Show the create E-Book form.
     */
    public function create()
    {
        return view('admin.ebooks.create');
    }

    /**
     * Temporary store handler – validates and redirects back.
     * You can replace this later with real persistence logic.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'pricing_type' => 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image',
            'banner_image' => 'nullable|image',
            'material' => 'nullable|file',
        ]);

        // TODO: Implement real save logic (model + upload handling)

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-Book form submitted (demo only).');
    }
}

