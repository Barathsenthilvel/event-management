<?php

namespace App\Http\Controllers;

use App\Models\Nomination;
use App\Models\NominationAlert;
use App\Models\NominationEntry;
use App\Models\NominationPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NominationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $nominations = Nomination::query()
            ->with(['creator:id,name', 'positions:id,nomination_id,position'])
            ->withCount('entries')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('title', 'like', '%' . $q . '%');
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.nominations.index', compact('nominations', 'q'));
    }

    public function create()
    {
        $members = User::query()->where('is_approved', true)->latest('id')->get(['id', 'name', 'email', 'mobile']);
        return view('admin.nominations.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($request, $validated) {
            $nomination = Nomination::create($this->buildPayload($request, $validated, true));
            foreach ($this->extractPositions($request) as $position) {
                $nomination->positions()->create($position);
            }
        });

        return redirect()->route('admin.nominations.index')->with('success', 'Nomination created successfully.');
    }

    public function edit(Nomination $nomination)
    {
        $nomination->load('positions');
        $members = User::query()->where('is_approved', true)->latest('id')->get(['id', 'name', 'email', 'mobile']);
        return view('admin.nominations.edit', compact('nomination', 'members'));
    }

    public function update(Request $request, Nomination $nomination)
    {
        $validated = $request->validate($this->rules($nomination->id));

        DB::transaction(function () use ($request, $validated, $nomination) {
            $nomination->update($this->buildPayload($request, $validated, false));
            $nomination->positions()->delete();
            foreach ($this->extractPositions($request) as $position) {
                $nomination->positions()->create($position);
            }
        });

        return redirect()->route('admin.nominations.index')->with('success', 'Nomination updated successfully.');
    }

    public function destroy(Nomination $nomination)
    {
        $nomination->delete();
        return back()->with('success', 'Nomination deleted.');
    }

    public function cancel(Nomination $nomination)
    {
        $nomination->update(['status' => 'cancelled', 'is_active' => false]);
        return back()->with('success', 'Nomination cancelled.');
    }

    public function toggleStatus(Nomination $nomination)
    {
        $nomination->update(['is_active' => !$nomination->is_active]);
        return back()->with('success', 'Display status updated.');
    }

    public function alertForm(Nomination $nomination, Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $members = User::query()
            ->where('is_approved', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('mobile', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $alertedIds = NominationAlert::query()->where('nomination_id', $nomination->id)->pluck('user_id')->all();

        return view('admin.nominations.alert', compact('nomination', 'members', 'alertedIds', 'q'));
    }

    public function alertStore(Nomination $nomination, Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:all,specific,leaders',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'notify_whatsapp' => 'nullable|boolean',
            'notify_sms' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
        ]);

        $notifyWhatsApp = $request->boolean('notify_whatsapp');
        $notifySms = $request->boolean('notify_sms');
        $notifyEmail = $request->boolean('notify_email');

        if (!$notifyWhatsApp && !$notifySms && !$notifyEmail) {
            return back()->withErrors(['notify_channel' => 'Select at least one notification channel.'])->withInput();
        }

        $target = $validated['target'];
        if ($target === 'all') {
            $memberIds = User::query()->where('is_approved', true)->pluck('id')->all();
        } elseif ($target === 'leaders') {
            $memberIds = User::query()
                ->where('is_approved', true)
                ->whereNotNull('currently_working')
                ->where('currently_working', '!=', '')
                ->pluck('id')
                ->all();
        } else {
            $memberIds = array_values(array_unique($validated['member_ids'] ?? []));
        }

        if (empty($memberIds)) {
            return back()->withErrors(['member_ids' => 'Please select at least one member.'])->withInput();
        }

        $positionIds = NominationPosition::query()
            ->where('nomination_id', $nomination->id)
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($memberIds, $positionIds, $nomination, $notifyWhatsApp, $notifySms, $notifyEmail) {
            foreach ($memberIds as $userId) {
                NominationAlert::updateOrCreate(
                    ['nomination_id' => $nomination->id, 'user_id' => $userId],
                    [
                        'notify_whatsapp' => $notifyWhatsApp,
                        'notify_sms' => $notifySms,
                        'notify_email' => $notifyEmail,
                        'alert_sent_at' => now(),
                    ]
                );

                foreach ($positionIds as $positionId) {
                    NominationEntry::firstOrCreate(
                        ['nomination_id' => $nomination->id, 'position_id' => $positionId, 'user_id' => $userId],
                        ['submitted_at' => now()]
                    );
                }
            }
        });

        return redirect()->route('admin.nominations.submissions', $nomination->id)->with('success', 'Nomination alert sent successfully.');
    }

    public function submissions(Nomination $nomination, Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $entries = NominationEntry::query()
            ->with([
                'user:id,name,email,mobile',
                'position:id,nomination_id,position',
            ])
            ->where('nomination_id', $nomination->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('user', function ($userQ) use ($q) {
                    $userQ->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('mobile', 'like', '%' . $q . '%');
                })->orWhereHas('position', function ($posQ) use ($q) {
                    $posQ->where('position', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $positions = NominationPosition::query()
            ->where('nomination_id', $nomination->id)
            ->withCount('entries')
            ->get();

        return view('admin.nominations.submissions', compact('nomination', 'entries', 'positions', 'q'));
    }

    public function downloadReport(Nomination $nomination)
    {
        $rows = NominationEntry::query()
            ->with(['user:id,name,email,mobile', 'position:id,position'])
            ->where('nomination_id', $nomination->id)
            ->get();

        $csv = "Position,Member Name,Email,Mobile,Submitted On\n";
        foreach ($rows as $row) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                str_replace('"', '""', (string) ($row->position->position ?? '')),
                str_replace('"', '""', (string) ($row->user->name ?? '')),
                str_replace('"', '""', (string) ($row->user->email ?? '')),
                str_replace('"', '""', (string) ($row->user->mobile ?? '')),
                optional($row->submitted_at)->format('d M Y h:i A') ?? ''
            );
        }

        $fileName = 'nomination-' . $nomination->id . '-report.csv';
        $tmpPath = 'reports/' . $fileName;
        Storage::disk('local')->put($tmpPath, $csv);

        return response()->download(storage_path('app/' . $tmpPath), $fileName)->deleteFileAfterSend(true);
    }

    private function rules(?int $id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'terms' => 'nullable|string',
            'polling_date' => 'required|date',
            'polling_from' => 'required|date_format:H:i',
            'polling_to' => 'required|date_format:H:i|after:polling_from',
            'cover_image' => 'nullable|image|max:5120',
            'banner_image' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,active,closed,cancelled',
            'is_active' => 'nullable|boolean',
            'positions' => 'required|array|min:1',
            'positions.*.position' => 'required|string|max:255',
            'positions.*.member_user_id' => 'nullable|integer|exists:users,id',
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating): array
    {
        $payload = [
            'title' => $validated['title'],
            'terms' => $validated['terms'] ?? null,
            'polling_date' => $validated['polling_date'],
            'polling_from' => $validated['polling_from'],
            'polling_to' => $validated['polling_to'],
            'status' => $validated['status'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $request->file('cover_image')->store('nominations/covers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $payload['banner_image_path'] = $request->file('banner_image')->store('nominations/banners', 'public');
        }

        return $payload;
    }

    private function extractPositions(Request $request): array
    {
        $positions = [];
        foreach ((array) $request->input('positions', []) as $row) {
            if (!is_array($row) || empty($row['position'])) {
                continue;
            }
            $positions[] = [
                'position' => (string) $row['position'],
                'member_user_id' => !empty($row['member_user_id']) ? (int) $row['member_user_id'] : null,
            ];
        }
        return $positions;
    }
}

