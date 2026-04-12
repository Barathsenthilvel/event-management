<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\EventPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

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

        return view('admin.events.index', compact('events', 'q'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
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

    public function show(Event $event)
    {
        $event->load([
            'dates',
            'creator:id,name',
            'invites.user:id,name,email,mobile',
            'interests' => fn ($q) => $q->orderBy('created_at'),
        ]);

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $event->load('dates');

        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
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
                'start_time' => $item['start_time'] ?: null,
                'end_time' => $item['end_time'] ?: null,
            ];
        }

        return $result;
    }
}
