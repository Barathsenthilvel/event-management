<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\EventPhoto;
use App\Models\GnatNotificationBatch;
use App\Models\User;
use App\Services\EventScheduleStatusService;
use App\Jobs\SendGnatBulkNotificationChunkJob;
use App\Services\GnatMailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        if (Schema::hasTable('events')) {
            resolve(EventScheduleStatusService::class)->syncAll();
        }

        $q = trim((string) $request->query('q', ''));
        $interestTab = trim((string) $request->query('interest_tab', 'members'));
        if (! in_array($interestTab, ['members', 'guests'], true)) {
            $interestTab = 'members';
        }

        $events = Event::query()
            ->with(['creator:id,name', 'dates:id,event_id,event_date,start_time,end_time'])
            ->withCount('invites')
            ->withCount([
                'invites as member_interested_count' => fn ($query) => $query->whereNotNull('participation_status'),
                'invites as member_participated_count' => fn ($query) => $query->where('participation_status', 'participated'),
                'interests as public_interested_count' => fn ($query) => $query->whereNull('user_id'),
                'interests as public_participated_count' => fn ($query) => $query
                    ->whereNull('user_id')
                    ->where('participation_status', 'participated'),
            ])
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

        $event = null;
        DB::transaction(function () use ($request, $validated, &$event) {
            $event = Event::create($this->buildEventPayload($request, $validated, true));

            $dates = $this->extractDates($request);
            foreach ($dates as $date) {
                $event->dates()->create($date);
            }
        });

        if ($event) {
            $this->refreshEventStatusFromSchedule($event);
        }

        return redirect()
            ->route('admin.events.invite', $event)
            ->with('success', 'Event created successfully. Invite approved members now.');
    }

    public function show(Request $request, Event $event)
    {
        $this->refreshEventStatusFromSchedule($event);

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

        $inviteUserIds = $event->invites->pluck('user_id')->filter()->map(fn ($id) => (int) $id)->unique()->all();

        $unifiedAttendeeRows = collect();
        foreach ($event->invites as $invite) {
            $unifiedAttendeeRows->push([
                'kind' => 'invite',
                'invite' => $invite,
                'interest' => null,
            ]);
        }
        foreach ($event->interests as $interest) {
            if ($interest->user_id && in_array((int) $interest->user_id, $inviteUserIds, true)) {
                continue;
            }
            $unifiedAttendeeRows->push([
                'kind' => 'interest',
                'invite' => null,
                'interest' => $interest,
            ]);
        }

        $unifiedAttendeeRows = $unifiedAttendeeRows
            ->sortBy(function (array $r) {
                if ($r['kind'] === 'invite') {
                    return strtolower((string) ($r['invite']->user->name ?? ''));
                }

                return strtolower((string) ($r['interest']->name ?? ''));
            })
            ->values();

        $unifiedCountAll = $unifiedAttendeeRows->count();
        $unifiedCountMembers = $unifiedAttendeeRows->filter(function (array $r) {
            if ($r['kind'] === 'invite') {
                return true;
            }

            return ! empty($r['interest']->user_id);
        })->count();
        $unifiedCountGuests = $unifiedAttendeeRows->filter(fn (array $r) => $r['kind'] === 'interest' && empty($r['interest']->user_id))->count();

        $filteredUnifiedRows = match ($interestType) {
            'members' => $unifiedAttendeeRows->filter(function (array $r) {
                if ($r['kind'] === 'invite') {
                    return true;
                }

                return ! empty($r['interest']->user_id);
            })->values(),
            'non_members' => $unifiedAttendeeRows->filter(fn (array $r) => $r['kind'] === 'interest' && empty($r['interest']->user_id))->values(),
            default => $unifiedAttendeeRows,
        };

        return view('admin.events.show', compact(
            'event',
            'interestType',
            'filteredUnifiedRows',
            'unifiedCountAll',
            'unifiedCountMembers',
            'unifiedCountGuests'
        ));
    }

    public function edit(Event $event)
    {
        $event->load('dates');
        $this->refreshEventStatusFromSchedule($event);

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

        $this->refreshEventStatusFromSchedule($event);
        $event->refresh();

        return redirect()
            ->route('admin.events.invite', $event)
            ->with('success', 'Event updated successfully. Invite or re-notify approved members below.');
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
        $event->refresh();
        $this->refreshEventStatusFromSchedule($event);
        $event->refresh();

        return redirect()->route('admin.events.index')->with('success', 'Display status updated.');
    }

    public function sendReminder(Request $request, Event $event)
    {
        if ($redirect = $this->gateEventReminder($event)) {
            return $redirect;
        }

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
            return redirect()
                ->route('admin.events.invite', ['event' => $event, 'reminder' => 1])
                ->withErrors(['notify_channel' => 'Select at least one notification channel.'])
                ->withInput();
        }

        $eligibleQuery = $this->eventReminderEligibleInvitesQuery($event);
        $eligibleUserIds = (clone $eligibleQuery)->pluck('user_id')->unique()->map(fn ($id) => (int) $id)->values()->all();

        if ($eligibleUserIds === []) {
            return redirect()
                ->route('admin.events.invite', ['event' => $event, 'reminder' => 1])
                ->with('warning', 'No invited members were found for this event. Send invites first.');
        }

        if ($validated['target'] === 'all') {
            $selectedUserIds = $eligibleUserIds;
        } else {
            $picked = array_values(array_unique(array_map('intval', $validated['member_ids'] ?? [])));
            $selectedUserIds = array_values(array_intersect($eligibleUserIds, $picked));
            if ($selectedUserIds === []) {
                return redirect()
                    ->route('admin.events.invite', ['event' => $event, 'reminder' => 1])
                    ->withErrors(['member_ids' => 'Select at least one invited member from the list.'])
                    ->withInput();
            }
        }

        $invites = (clone $eligibleQuery)
            ->whereIn('user_id', $selectedUserIds)
            ->get(['id', 'user_id']);

        if ($invites->isEmpty()) {
            return redirect()
                ->route('admin.events.invite', ['event' => $event, 'reminder' => 1])
                ->with('warning', 'No matching invites were found for the members you selected.');
        }

        $userIds = $invites->pluck('user_id')->unique()->values()->map(fn ($id) => (int) $id)->all();
        $inviteIds = $invites->pluck('id')->all();

        $chunkSize = 200;
        $batch = GnatNotificationBatch::start(
            auth('admin')->id(),
            SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITE_REMINDERS,
            (int) $event->id,
            (string) $event->title,
            count($userIds),
            $chunkSize,
            [
                'reminder' => true,
                'reminder_target' => $validated['target'],
                'notify_email' => $notifyEmail,
                'notify_sms' => $notifySms,
                'notify_whatsapp' => $notifyWhatsApp,
            ]
        );

        SendGnatBulkNotificationChunkJob::dispatchChunks(
            SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITE_REMINDERS,
            (int) $event->id,
            $userIds,
            $notifyEmail,
            $notifySms,
            $notifyWhatsApp,
            $chunkSize,
            $batch->id
        );

        EventInvite::query()
            ->whereIn('id', $inviteIds)
            ->update(['reminder_sent_at' => now()]);

        $success = $this->formatAdminReminderQueuedMessage(
            'Event',
            count($userIds),
            $notifyEmail,
            $notifySms,
            $notifyWhatsApp
        );

        return redirect()
            ->route('admin.events.invite', ['event' => $event, 'reminder' => 1])
            ->with('success', $success);
    }

    public function attendanceScanner(Event $event)
    {
        $this->refreshEventStatusFromSchedule($event);

        return view('admin.events.attendance-scanner', compact('event'));
    }

    public function consumeAttendanceQr(Request $request, Event $event, string $source, int $entryId)
    {
        $this->refreshEventStatusFromSchedule($event);

        if ($event->status === 'cancelled') {
            return response()->json([
                'ok' => false,
                'message' => 'Event is cancelled. Attendance cannot be updated.',
            ], 422);
        }

        if (! in_array($event->status, ['live', 'completed'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Set event status to Live or Completed before scanning attendance.',
            ], 422);
        }

        if (! in_array($source, ['invite', 'interest'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid QR source.',
            ], 422);
        }

        if ($source === 'invite') {
            $invite = EventInvite::query()
                ->with('user:id,name,email')
                ->where('event_id', $event->id)
                ->where('id', $entryId)
                ->first();
            if (! $invite) {
                return response()->json(['ok' => false, 'message' => 'Invite record not found.'], 404);
            }

            $alreadyAttended = $invite->participation_status === 'participated';
            if (! $alreadyAttended) {
                $invite->update(['participation_status' => 'participated']);
                if ($invite->user) {
                    app(GnatMailService::class)->sendEventParticipationConfirmation($invite->user, $event);
                }
            }

            return response()->json([
                'ok' => true,
                'message' => $alreadyAttended ? 'Already marked as attended.' : 'Member attendance marked as attended.',
                'who' => $invite->user->name ?? ('Member #'.$invite->user_id),
                'source' => 'member',
            ]);
        }

        $interest = EventInterest::query()
            ->where('event_id', $event->id)
            ->where('id', $entryId)
            ->first();
        if (! $interest) {
            return response()->json(['ok' => false, 'message' => 'Public registration record not found.'], 404);
        }

        $alreadyAttended = $interest->participation_status === 'participated';
        if (! $alreadyAttended) {
            $interest->update(['participation_status' => 'participated']);
            if ($interest->email || $interest->phone) {
                app(GnatMailService::class)->sendEventParticipationConfirmationByEmail(
                    (string) ($interest->email ?? ''),
                    (string) ($interest->name ?: 'Guest'),
                    $event,
                    $interest->phone ? (string) $interest->phone : null
                );
            }
        }

        return response()->json([
            'ok' => true,
            'message' => $alreadyAttended ? 'Already marked as attended.' : 'Public attendee marked as attended.',
            'who' => $interest->name ?: ('Public #'.$interest->id),
            'source' => 'public',
        ]);
    }

    public function updateInviteStatus(Request $request, Event $event, EventInvite $invite)
    {
        if ($invite->event_id !== $event->id) {
            abort(404);
        }

        $this->refreshEventStatusFromSchedule($event);

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

        $wasParticipated = $invite->participation_status === 'participated';

        $invite->update([
            'participation_status' => $validated['participation_status'],
            'has_confirmed_interest' => true,
        ]);

        if (
            $validated['participation_status'] === 'participated'
            && ! $wasParticipated
            && $invite->user
        ) {
            app(GnatMailService::class)->sendEventParticipationConfirmation($invite->user, $event);
        }

        return back()->with('success', 'Participation status updated.');
    }

    public function updateInterestAttendance(Request $request, Event $event, EventInterest $interest)
    {
        if ($interest->event_id !== $event->id) {
            abort(404);
        }

        $this->refreshEventStatusFromSchedule($event);

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

        $wasParticipated = $interest->participation_status === 'participated';

        $interest->update([
            'participation_status' => $validated['participation_status'],
        ]);

        if (
            $validated['participation_status'] === 'participated'
            && ! $wasParticipated
            && ($interest->email || $interest->phone)
        ) {
            app(GnatMailService::class)->sendEventParticipationConfirmationByEmail(
                (string) ($interest->email ?? ''),
                (string) ($interest->name ?: 'Guest'),
                $event,
                $interest->phone ? (string) $interest->phone : null
            );
        }

        return back()->with('success', 'Public registration attendance updated.');
    }

    public function downloadInterestCertificate(Event $event, EventInterest $interest)
    {
        if ($interest->event_id !== $event->id) {
            abort(404);
        }

        $this->refreshEventStatusFromSchedule($event);

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

        $this->refreshEventStatusFromSchedule($event);

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
        $this->refreshEventStatusFromSchedule($event);
        $event->refresh();

        $reminderMode = $request->boolean('reminder');
        if ($reminderMode) {
            if (! $event->is_active || ! in_array($event->status, ['upcoming', 'live'], true)) {
                return redirect()
                    ->route('admin.events.index')
                    ->with('error', 'Reminders are only available when the event display is Active and the status is Upcoming or Live.');
            }
        }

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

        return view('admin.events.invite', compact('event', 'members', 'q', 'reminderMode'));
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
            $invite = EventInvite::query()->firstOrNew([
                'event_id' => $event->id,
                'user_id' => $userId,
            ]);

            $hadConfirmed = $invite->exists && $invite->has_confirmed_interest;

            $invite->fill([
                'notify_whatsapp' => $notifyWhatsApp,
                'notify_sms' => $notifySms,
                'notify_email' => $notifyEmail,
                'invited_at' => $now,
            ]);

            if (! $invite->exists) {
                $invite->participation_status = 'interested';
                $invite->has_confirmed_interest = false;
            } elseif (! $hadConfirmed) {
                $invite->has_confirmed_interest = false;
            }

            $invite->save();
        }

        $event->syncInterestedCountFromRegistrations();

        $intUserIds = array_map('intval', $userIds);
        $chunkSize = 200;
        $batch = GnatNotificationBatch::start(
            auth('admin')->id(),
            SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITES,
            (int) $event->id,
            (string) $event->title,
            count($intUserIds),
            $chunkSize,
            [
                'notify_email' => $notifyEmail,
                'notify_sms' => $notifySms,
                'notify_whatsapp' => $notifyWhatsApp,
            ]
        );

        SendGnatBulkNotificationChunkJob::dispatchChunks(
            SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITES,
            (int) $event->id,
            $intUserIds,
            true,
            false,
            false,
            $chunkSize,
            $batch->id
        );

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Invitations have been queued and will be sent in the background.');
    }

    public function album(Event $event)
    {
        $event->load(['photos' => fn ($q) => $q->latest('id')]);

        return view('admin.events.album', compact('event'));
    }

    public function albumStore(Request $request, Event $event)
    {
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
                'start_time' => ! empty($item['start_time']) ? $this->normalizeHiTime($item['start_time']) : null,
                'end_time' => ! empty($item['end_time']) ? $this->normalizeHiTime($item['end_time']) : null,
            ];
        }

        return $result;
    }

    private function mergeNormalizedEventTimes(Request $request): void
    {
        $rows = (array) $request->input('event_dates', []);
        foreach ($rows as $idx => $row) {
            if (! is_array($row)) {
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

    /**
     * Recompute this event's status (upcoming / live / completed) from its dates — no scheduler required.
     */
    private function refreshEventStatusFromSchedule(Event $event): void
    {
        if (! Schema::hasTable('events')) {
            return;
        }

        $event->loadMissing('dates');
        resolve(EventScheduleStatusService::class)->syncOne($event);
        $event->refresh();
    }

    /**
     * Invited members (have been sent an invite at least once).
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\EventInvite>
     */
    private function eventReminderEligibleInvitesQuery(Event $event)
    {
        return EventInvite::query()
            ->where('event_id', $event->id)
            ->whereNotNull('invited_at');
    }

    private function gateEventReminder(Event $event): ?\Illuminate\Http\RedirectResponse
    {
        $this->refreshEventStatusFromSchedule($event);
        $event->refresh();

        if (! $event->is_active) {
            return redirect()
                ->route('admin.events.invite', $event)
                ->with('error', 'Reminders can only be sent when the event display status is Active.');
        }

        if (! in_array($event->status, ['upcoming', 'live'], true)) {
            return redirect()
                ->route('admin.events.invite', $event)
                ->with('error', 'Reminders can only be sent for events in Upcoming or Live status.');
        }

        return null;
    }

    private function formatAdminReminderQueuedMessage(
        string $entityLabel,
        int $memberCount,
        bool $notifyEmail,
        bool $notifySms,
        bool $notifyWhatsApp
    ): string {
        $channels = [];
        if ($notifyEmail) {
            $channels[] = 'email';
        }
        if ($notifySms) {
            $channels[] = 'SMS';
        }
        if ($notifyWhatsApp) {
            $channels[] = 'WhatsApp';
        }
        $via = $channels === []
            ? 'your selected channels'
            : implode(', ', $channels);

        return $entityLabel.' reminders have been queued for '.$memberCount.' member(s) via '.$via.'. Delivery runs in the background; each member only receives channels they agreed to when invited.';
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
}
