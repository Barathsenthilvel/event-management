<?php

namespace App\Http\Controllers;

use App\Models\EBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EBookController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $ebooks = EBook::query()
            ->with('creator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%' . $q . '%')
                        ->orWhere('code', 'like', '%' . $q . '%')
                        ->orWhere('hospital', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.ebooks.index', compact('ebooks', 'q'));
    }

    public function create()
    {
        return view('admin.ebooks.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        EBook::create($this->buildPayload($request, $validated, true));

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-Book created successfully.');
    }

    public function edit(EBook $e_book)
    {
        return view('admin.ebooks.edit', ['ebook' => $e_book]);
    }

    public function update(Request $request, EBook $e_book)
    {
        $validated = $this->validatePayload($request, $e_book->id);
        $e_book->update($this->buildPayload($request, $validated, false, $e_book));

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-Book updated successfully.');
    }

    public function destroy(EBook $e_book)
    {
        $e_book->delete();

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-Book deleted successfully.');
    }

    public function togglePromote(EBook $e_book)
    {
        $e_book->update(['promote_front' => !$e_book->promote_front]);

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'Promote front updated.');
    }

    public function toggleStatus(EBook $e_book)
    {
        $e_book->update(['is_active' => !$e_book->is_active]);

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'Display status updated.');
    }

    private function validatePayload(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'hospital' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'pricing_type' => 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0|required_if:pricing_type,paid',
            'cover_image' => 'nullable|image|max:5120',
            'banner_image' => 'nullable|image|max:5120',
            'material' => 'nullable|file|mimes:pdf,doc,docx,zip|max:15360',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function buildPayload(Request $request, array $validated, bool $creating, ?EBook $ebook = null): array
    {
        $payload = [
            'title' => $validated['title'],
            'hospital' => $validated['hospital'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'pricing_type' => $validated['pricing_type'],
            'price' => $validated['pricing_type'] === 'paid' ? (float) $validated['price'] : 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
            $payload['code'] = $this->generateCode();
            $payload['promote_front'] = false;
        }

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $request->file('cover_image')->store('ebooks/covers', 'public');
        } elseif ($ebook) {
            $payload['cover_image_path'] = $ebook->cover_image_path;
        }

        if ($request->hasFile('banner_image')) {
            $payload['banner_image_path'] = $request->file('banner_image')->store('ebooks/banners', 'public');
        } elseif ($ebook) {
            $payload['banner_image_path'] = $ebook->banner_image_path;
        }

        if ($request->hasFile('material')) {
            $payload['material_path'] = $request->file('material')->store('ebooks/materials', 'public');
        } elseif ($ebook) {
            $payload['material_path'] = $ebook->material_path;
        }

        return $payload;
    }

    private function generateCode(): string
    {
        $nextId = ((int) EBook::max('id')) + 1;
        return 'EBK-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
    }
}

