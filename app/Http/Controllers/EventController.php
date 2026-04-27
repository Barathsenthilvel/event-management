<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\EventPhoto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $interestTab = trim((string) $request->query('interest_tab', 'members'));
        if (! in_array($interestTab, ['members', 'guests'], true)) {
            $interestTab = 'members';
        }

        $events = Event::query()
            ->with(['creator:id,name', 'dates:id,event_id,event_date,start_time,end_time'])
            ->withCount('invites')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('venue', 'like', '%'.$q.'%')
                        ->orWhere('status', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        if ($interestTab === 'members') {
            $interestRows = EventInvite::query()
                ->with(['event:id,title', 'user:id,name,email,mobile'])
                ->whereNotNull('participation_status')
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->whereHas('event', fn ($eventQuery) => $eventQuery->where('title', 'like', '%'.$q.'%'))
                            ->orWhereHas('user', function ($userQuery) use ($q) {
                                $userQuery->where('name', 'like', '%'.$q.'%')
                                    ->orWhere('email', 'like', '%'.$q.'%')
                                    ->orWhere('mobile', 'like', '%'.$q.'%');
                            });
                    });
                })
                ->latest('id')
                ->paginate(12, ['*'], 'interest_page')
                ->withQueryString();
        } else {
            $interestRows = EventInterest::query()
                ->with(['event:id,title'])
                ->whereNull('user_id')
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('name', 'like', '%'.$q.'%')
                            ->orWhere('email', 'like', '%'.$q.'%')
                            ->orWhere('phone', 'like', '%'.$q.'%')
                            ->orWhereHas('event', fn ($eventQuery) => $eventQuery->where('title', 'like', '%'.$q.'%'));
                    });
                })
                ->latest('id')
                ->paginate(12, ['*'], 'interest_page')
                ->withQueryString();
        }

        $memberInterestCount = EventInvite::query()->whereNotNull('participation_status')->count();
        $guestInterestCount = EventInterest::query()->whereNull('user_id')->count();

        return view('admin.events.index', compact(
            'events',
            'q',
            'interestTab',
            'interestRows',
            'memberInterestCount',
            'guestInterestCount'
        ));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $this->mergeNormalizedEventTimes($request);
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($request, $validated) {
            $event = Event::create($this->buildEventPayload($request, $validated, true));

            $dates = $this->extractDates($request);
            foreach ($dates as $date) {
                $event->dates()->create($date);
            }
        });

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function show(Request $request, Event $event)
    {
        $event->load([
            'dates',
            'creator:id,name',
            'invites.user:id,name,email,mobile',
            'interests' => fn ($q) => $q->orderBy('created_at'),
        ]);

        $interestType = trim((string) $request->query('interest_type', 'all'));
        if (! in_array($interestType, ['all', 'members', 'non_members'], true)) {
            $interestType = 'all';
        }

        $allInterests = $event->interests->values();
        $memberInterests = $allInterests->filter(fn ($row) => ! empty($row->user_id))->values();
        $nonMemberInterests = $allInterests->filter(fn ($row) => empty($row->user_id))->values();
        $filteredInterests = match ($interestType) {
            'members' => $memberInterests,
            'non_members' => $nonMemberInterests,
            default => $allInterests,
        };

        return view('admin.events.show', compact(
            'event',
            'interestType',
            'filteredInterests',
            'memberInterests',
            'nonMemberInterests',
            'allInterests'
        ));
    }

    public function edit(Event $event)
    {
        $event->load('dates');

        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->mergeNormalizedEventTimes($request);
        $validated = $request->validate($this->rules($event->id));

        DB::transaction(function () use ($event, $request, $validated) {
            $event->update($this->buildEventPayload($request, $validated, false));

            $event->dates()->delete();
            foreach ($this->extractDates($request) as $date) {
                $event->dates()->create($date);
            }
        });

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    public function cancel(Event $event)
    {
        $event->update(['status' => 'cancelled', 'is_active' => false]);

        return redirect()->route('admin.events.index')->with('success', 'Event cancelled.');
    }

    public function togglePromote(Event $event)
    {
        $event->update(['promote_front' => ! $event->promote_front]);

        return redirect()->route('admin.events.index')->with('success', 'Event promotion status updated.');
    }

    public function toggleDisplay(Event $event)
    {
        $event->update(['is_active' => ! $event->is_active]);

        return redirect()->route('admin.events.index')->with('success', 'Display status updated.');
    }

    public function sendReminder(Event $event)
    {
        EventInvite::query()
            ->where('event_id', $event->id)
            ->update(['reminder_sent_at' => now()]);

        return redirect()->route('admin.events.index')->with('success', 'Reminder marked as sent.');
    }

    public function updateInviteStatus(Request $request, Event $event, EventInvite $invite)
    {
        if ($invite->event_id !== $event->id) {
            abort(404);
        }

        if ($event->status === 'cancelled') {
            return back()->with('error', 'Participation status cannot be changed for a cancelled event.');
        }

        if (! in_array($event->status, ['live', 'completed'], true)) {
            return back()->with(
                'error',
                'Attendance (attended / did not attend) can be updated only when the event is Live or Completed.'
            );
        }

        $validated = $request->validate([
            'participation_status' => 'required|in:interested,participated,not_participated',
        ]);

        $invite->update([
            'participation_status' => $validated['participation_status'],
        ]);

        return back()->with('success', 'Participation status updated.');
    }

    public function updateInterestAttendance(Request $request, Event $event, EventInterest $interest)
    {
        if ($interest->event_id !== $event->id) {
            abort(404);
        }

        if ($event->status === 'cancelled') {
            return back()->with('error', 'Attendance cannot be changed for a cancelled event.');
        }

        if (! in_array($event->status, ['live', 'completed'], true)) {
            return back()->with(
                'error',
                'Set the event to Live or Completed before recording attendance for public registrations.'
            );
        }

        $validated = $request->validate([
            'participation_status' => 'required|in:interested,participated,not_participated',
        ]);

        $interest->update([
            'participation_status' => $validated['participation_status'],
        ]);

        return back()->with('success', 'Public registration attendance updated.');
    }

    public function downloadInterestCertificate(Event $event, EventInterest $interest)
    {
        if ($interest->event_id !== $event->id) {
            abort(404);
        }

        if ($interest->participation_status !== 'participated') {
            return back()->with('error', 'Certificate is available only for attended registrations.');
        }

        if (empty($event->template_pdf_path) || ! Storage::disk('public')->exists($event->template_pdf_path)) {
            return back()->with('error', 'Certificate template PDF is not available for this event.');
        }

        $guestName = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) ($interest->name ?? 'guest'));
        $eventTitle = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $event->title);
        $fileName = trim("{$eventTitle}-{$guestName}-certificate.pdf", '-');

        return response()->download(
            Storage::disk('public')->path($event->template_pdf_path),
            $fileName
        );
    }

    public function downloadInviteCertificate(Event $event, EventInvite $invite)
    {
        if ($invite->event_id !== $event->id) {
            abort(404);
        }

        if ($invite->participation_status !== 'participated') {
            return back()->with('error', 'Certificate download is available only for participated members.');
        }

        if (empty($event->template_pdf_path) || ! Storage::disk('public')->exists($event->template_pdf_path)) {
            return back()->with('error', 'Certificate template PDF is not available for this event.');
        }

        $memberName = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) ($invite->user->name ?? 'member'));
        $eventTitle = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $event->title);
        $fileName = trim("{$eventTitle}-{$memberName}-certificate.pdf", '-');

        return response()->download(
            Storage::disk('public')->path($event->template_pdf_path),
            $fileName
        );
    }

    public function inviteForm(Event $event, Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $members = User::query()
            ->where('is_approved', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('mobile', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $invitedUserIds = EventInvite::query()
            ->where('event_id', $event->id)
            ->pluck('user_id')
            ->all();

        return view('admin.events.invite', compact('event', 'members', 'invitedUserIds', 'q'));
    }

    public function inviteStore(Event $event, Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:all,specific',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'notify_whatsapp' => 'nullable|boolean',
            'notify_sms' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
        ]);

        $notifyWhatsApp = $request->boolean('notify_whatsapp');
        $notifySms = $request->boolean('notify_sms');
        $notifyEmail = $request->boolean('notify_email');

        if (! $notifyWhatsApp && ! $notifySms && ! $notifyEmail) {
            return back()->withErrors([
                'notify_channel' => 'Select at least one notification channel.',
            ])->withInput();
        }

        $userIds = $validated['target'] === 'all'
            ? User::query()->where('is_approved', true)->pluck('id')->all()
            : array_values(array_unique($validated['member_ids'] ?? []));

        if (empty($userIds)) {
            return back()->withErrors([
                'member_ids' => 'Please select at least one member.',
            ])->withInput();
        }

        $now = now();
        foreach ($userIds as $userId) {
            EventInvite::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $userId],
                [
                    'notify_whatsapp' => $notifyWhatsApp,
                    'notify_sms' => $notifySms,
                    'notify_email' => $notifyEmail,
                    'invited_at' => $now,
                ]
            );
        }

        $event->update(['interested_count' => EventInvite::where('event_id', $event->id)->count()]);

        return redirect()->route('admin.events.index')->with('success', 'Members invited successfully.');
    }

    public function album(Event $event)
    {
        $event->load(['photos' => fn ($q) => $q->latest('id')]);

        return view('admin.events.album', compact('event'));
    }

    public function albumStore(Request $request, Event $event)
    {
        if ($event->status !== 'completed') {
            return back()->with('error', 'You can add album photos only after event completion.');
        }

        $validated = $request->validate([
            'photos' => 'required|array|min:1|max:20',
            'photos.*' => 'image|max:5120',
        ]);

        foreach ($validated['photos'] as $photo) {
            $path = $photo->store('events/albums', 'public');
            $event->photos()->create(['photo_path' => $path]);
        }

        return back()->with('success', 'Event album updated successfully.');
    }

    public function albumDestroy(Event $event, EventPhoto $photo)
    {
        if ($photo->event_id !== $event->id) {
            abort(404);
        }

        if (! empty($photo->photo_path) && Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        $photo->delete();

        return back()->with('success', 'Photo removed from album.');
    }

    private function rules(?int $eventId = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'seat_mode' => 'required|in:unlimited,limited',
            'seat_limit' => 'nullable|integer|min:1|required_if:seat_mode,limited',
            'status' => 'required|in:upcoming,live,completed,cancelled',
            'promote_front' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'cover_image' => 'nullable|image|max:5120',
            'banner_image' => 'nullable|image|max:5120',
            'template_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'event_dates' => 'required|array|min:1',
            'event_dates.*.date' => 'required|date',
            'event_dates.*.start_time' => 'nullable|date_format:H:i',
            'event_dates.*.end_time' => 'nullable|date_format:H:i',
        ];
    }

    private function buildEventPayload(Request $request, array $validated, bool $creating): array
    {
        $payload = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'seat_mode' => $validated['seat_mode'],
            'seat_limit' => $validated['seat_mode'] === 'limited' ? (int) $validated['seat_limit'] : null,
            'status' => $validated['status'],
            'promote_front' => $request->boolean('promote_front'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $request->file('cover_image')->store('events/covers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $payload['banner_image_path'] = $request->file('banner_image')->store('events/banners', 'public');
        }
        if ($request->hasFile('template_pdf')) {
            $payload['template_pdf_path'] = $request->file('template_pdf')->store('events/templates', 'public');
        }

        return $payload;
    }

    private function extractDates(Request $request): array
    {
        $result = [];
        foreach ((array) $request->input('event_dates', []) as $item) {
            if (! is_array($item) || empty($item['date'])) {
                continue;
            }

            $result[] = [
                'event_date' => $item['date'],
                'start_time' => !empty($item['start_time']) ? $this->normalizeHiTime($item['start_time']) : null,
                'end_time' => !empty($item['end_time']) ? $this->normalizeHiTime($item['end_time']) : null,
            ];
        }

        return $result;
    }

    private function mergeNormalizedEventTimes(Request $request): void
    {
        $rows = (array) $request->input('event_dates', []);
        foreach ($rows as $idx => $row) {
            if (!is_array($row)) {
                continue;
            }
            if (array_key_exists('start_time', $row)) {
                $rows[$idx]['start_time'] = $this->normalizeHiTime($row['start_time']);
            }
            if (array_key_exists('end_time', $row)) {
                $rows[$idx]['end_time'] = $this->normalizeHiTime($row['end_time']);
            }
        }
        $request->merge(['event_dates' => $rows]);
    }

    private function normalizeHiTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return is_string($value) ? '' : null;
        }
        $value = trim((string) $value);
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $value, $m)) {
            return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }
        if (preg_match('/^\d{1,2}:\d{2}\s?(AM|PM)$/i', $value)) {
            try {
                return Carbon::createFromFormat('g:i A', strtoupper(str_replace('.', '', $value)))->format('H:i');
            } catch (\Throwable $e) {
                return $value;
            }
        }
        return $value;
    }

    private function syncElapsedEvents(): void
    {
        $activeEvents = Event::query()
            ->whereIn('status', ['upcoming', 'live'])
            ->with(['dates:id,event_id,event_date,start_time,end_time'])
            ->get(['id', 'status', 'is_active']);

        $now = now();
        foreach ($activeEvents as $event) {
            $scheduleWindows = $event->dates
                ->filter(fn ($row) => ! empty($row->event_date))
                ->map(function ($row) {
                    $eventDay = Carbon::parse($row->event_date)->startOfDay();
                    $startTime = $row->start_time ?: '00:00';
                    $endTime = $row->end_time ?: '23:59';

                    $startAt = Carbon::parse($eventDay->format('Y-m-d').' '.$startTime);
                    $endAt = Carbon::parse($eventDay->format('Y-m-d').' '.$endTime);

                    // Handle overnight slot (e.g. 11:30 PM to 01:00 AM next day).
                    if (! empty($row->start_time) && ! empty($row->end_time) && $endAt->lessThanOrEqualTo($startAt)) {
                        $endAt->addDay();
                    }

                    return [
                        'start' => $startAt,
                        'end' => $endAt,
                    ];
                })
                ->values();

            if ($scheduleWindows->isEmpty()) {
                continue;
            }

            $start = $scheduleWindows->min('start');
            $end = $scheduleWindows->max('end');

            if ($now->greaterThan($end)) {
                $event->update([
                    'status' => 'completed',
                    'is_active' => false,
                ]);
                continue;
            }

            if ($now->greaterThanOrEqualTo($start) && $event->status !== 'live') {
                $event->update(['status' => 'live']);
                continue;
            }

            if ($now->lt($start) && $event->status !== 'upcoming') {
                $event->update(['status' => 'upcoming']);
            }
        }
    }
}
