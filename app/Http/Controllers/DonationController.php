<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $donations = Donation::query()
            ->with('creator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('purpose', 'like', '%'.$q.'%')
                        ->orWhere('short_description', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.donations.index', [
            'donations' => $donations,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.donations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules($request));
        $this->assertCustomPillTagsFilled($validated);

        DB::transaction(function () use ($request, $validated) {
            Donation::create($this->buildPayload($request, $validated, true));
        });

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation created successfully.');
    }

    public function edit(Donation $donation)
    {
        return view('admin.donations.edit', compact('donation'));
    }

    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate($this->rules($request, $donation->id));
        $this->assertCustomPillTagsFilled($validated);

        $payload = $this->buildPayload($request, $validated, false, $donation);
        $donation->update($payload);

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation updated successfully.');
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }

    public function togglePromote(Donation $donation)
    {
        $donation->update(['promote_front' => ! $donation->promote_front]);

        return redirect()->route('admin.donations.index')->with('success', 'Promote front updated.');
    }

    public function toggleStatus(Donation $donation)
    {
        $donation->update(['is_active' => ! $donation->is_active]);

        return redirect()->route('admin.donations.index')->with('success', 'Display status updated.');
    }

    private function rules(Request $request, ?int $id = null): array
    {
        $creating = $id === null;
        $coverRules = $creating
            ? ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120']
            : ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'];

        $pillIn = Rule::in(Donation::PILL_SOURCES);

        return [
            'purpose' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:65535'],
            'cover_image' => $coverRules,
            'banner_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'promote_front' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'pill_tag_1_source' => ['required', 'string', $pillIn],
            'pill_tag_1_custom' => [
                Rule::requiredIf(fn () => (string) $request->input('pill_tag_1_source') === 'custom'),
                'nullable',
                'string',
                'max:48',
            ],
            'pill_tag_2_source' => ['required', 'string', $pillIn],
            'pill_tag_2_custom' => [
                Rule::requiredIf(fn () => (string) $request->input('pill_tag_2_source') === 'custom'),
                'nullable',
                'string',
                'max:48',
            ],
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating, ?Donation $donation = null): array
    {
        $pill1 = Donation::pillLabelFromSource(
            $validated['pill_tag_1_source'],
            $validated['pill_tag_1_custom'] ?? null
        );
        $pill2 = Donation::pillLabelFromSource(
            $validated['pill_tag_2_source'],
            $validated['pill_tag_2_custom'] ?? null
        );

        $payload = [
            'purpose' => $validated['purpose'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'pill_tag_1' => $pill1,
            'pill_tag_2' => $pill2,
            'promote_front' => $request->boolean('promote_front'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $request->file('cover_image')->store('donations/covers', 'public');
        } elseif ($donation) {
            $payload['cover_image_path'] = $donation->cover_image_path;
        }

        if ($request->hasFile('banner_image')) {
            $payload['banner_image_path'] = $request->file('banner_image')->store('donations/banners', 'public');
        } elseif ($donation) {
            $payload['banner_image_path'] = $donation->banner_image_path;
        }

        return $payload;
    }

    private function assertCustomPillTagsFilled(array $validated): void
    {
        $errors = [];
        foreach ([1, 2] as $i) {
            $srcKey = "pill_tag_{$i}_source";
            $customKey = "pill_tag_{$i}_custom";
            if (($validated[$srcKey] ?? '') === 'custom' && trim((string) ($validated[$customKey] ?? '')) === '') {
                $errors[$customKey] = ['Please enter text for this custom tag.'];
            }
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
