<?php

namespace App\Http\Controllers;

use App\Services\GnatMailService;
use App\Jobs\SendGnatBulkNotificationChunkJob;
use App\Models\GnatNotificationBatch;
use App\Models\Meeting;
use App\Models\MeetingInvite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $this->syncElapsedMeetings();

        $q = trim((string) $request->query('q', ''));

        $meetings = Meeting::query()
            ->with(['creator:id,name', 'schedules:id,meeting_id,meeting_date,from_time,to_time'])
            ->withCount('invites')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('meeting_mode', 'like', '%'.$q.'%')
                        ->orWhere('status', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.meetings.index', compact('meetings', 'q'));
    }

    public function create()
    {
        $this->syncElapsedMeetings();
        return view('admin.meetings.create');
    }

    public function duplicate(Meeting $meeting)
    {
        $meeting->load('schedules');
        $schedule = $meeting->schedules->first();

        return view('admin.meetings.create', [
            'duplicateSource' => [
                'title' => $meeting->title,
                'meeting_link' => $meeting->meeting_link,
                'description' => $meeting->description,
                'meeting_mode' => $meeting->meeting_mode,
                'status' => 'upcoming',
                'is_active' => true,
                'schedule_date' => $schedule?->meeting_date?->format('Y-m-d'),
                'schedule_from' => $schedule?->from_time
                    ? Carbon::parse((string) $schedule->from_time)->format('H:i')
                    : null,
                'schedule_to' => $schedule?->to_time
                    ? Carbon::parse((string) $schedule->to_time)->format('H:i')
                    : null,
            ],
            'duplicateSourceMeeting' => $meeting,
        ]);
    }

    public function store(Request $request)
    {
        $this->mergeNormalizedScheduleTimes($request);
        $validated = $request->validate($this->rules());

        $meeting = DB::transaction(function () use ($request, $validated) {
            $meeting = Meeting::create($this->buildPayload($request, $validated, true));
            $schedule = $this->extractSchedule($request);
            $meeting->schedules()->create($schedule);

            return $meeting;
        });

        return redirect()
            ->route('admin.meetings.invite', $meeting->id)
            ->with('success', 'Meeting created successfully. Invite approved members now.');
    }

    public function edit(Meeting $meeting)
    {
        $this->syncElapsedMeetings();
        $meeting->load('schedules');

        return view('admin.meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $this->mergeNormalizedScheduleTimes($request);
        $validated = $request->validate($this->rules($meeting->id));

        DB::transaction(function () use ($request, $validated, $meeting) {
            $meeting->update($this->buildPayload($request, $validated, false));
            $meeting->schedules()->delete();
            $meeting->schedules()->create($this->extractSchedule($request));
        });

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting deleted successfully.');
    }

    public function cancel(Meeting $meeting)
    {
        $meeting->update(['status' => 'cancelled', 'is_active' => false]);

        try {
            app(GnatMailService::class)->sendMeetingCancelled($meeting->fresh());
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting cancelled and members notified.');
    }

    public function sendReminder(Request $request, Meeting $meeting)
    {
        if ($redirect = $this->gateMeetingReminder($meeting)) {
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
                ->route('admin.meetings.invite', ['meeting' => $meeting, 'reminder' => 1])
                ->withErrors(['notify_channel' => 'Select at least one notification channel.'])
                ->withInput();
        }

        $eligibleQuery = $this->meetingReminderEligibleInvitesQuery($meeting);
        $eligibleUserIds = (clone $eligibleQuery)->pluck('user_id')->unique()->map(fn ($id) => (int) $id)->values()->all();

        if ($eligibleUserIds === []) {
            return redirect()
                ->route('admin.meetings.invite', ['meeting' => $meeting, 'reminder' => 1])
                ->with('warning', 'No invited members were found for this meeting. Send invites first.');
        }

        if ($validated['target'] === 'all') {
            $selectedUserIds = $eligibleUserIds;
        } else {
            $picked = array_values(array_unique(array_map('intval', $validated['member_ids'] ?? [])));
            $selectedUserIds = array_values(array_intersect($eligibleUserIds, $picked));
            if ($selectedUserIds === []) {
                return redirect()
                    ->route('admin.meetings.invite', ['meeting' => $meeting, 'reminder' => 1])
                    ->withErrors(['member_ids' => 'Select at least one invited member from the list.'])
                    ->withInput();
            }
        }

        $invites = (clone $eligibleQuery)
            ->whereIn('user_id', $selectedUserIds)
            ->get(['id', 'user_id']);

        if ($invites->isEmpty()) {
            return redirect()
                ->route('admin.meetings.invite', ['meeting' => $meeting, 'reminder' => 1])
                ->with('warning', 'No matching invites were found for the members you selected.');
        }

        $userIds = $invites->pluck('user_id')->unique()->values()->map(fn ($id) => (int) $id)->all();
        $inviteIds = $invites->pluck('id')->all();

        $chunkSize = 200;
        $batch = GnatNotificationBatch::start(
            auth('admin')->id(),
            SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITE_REMINDERS,
            (int) $meeting->id,
            (string) $meeting->title,
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
            SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITE_REMINDERS,
            (int) $meeting->id,
            $userIds,
            $notifyEmail,
            $notifySms,
            $notifyWhatsApp,
            $chunkSize,
            $batch->id
        );

        MeetingInvite::query()
            ->whereIn('id', $inviteIds)
            ->update(['reminder_sent_at' => now()]);

        $success = $this->formatAdminReminderQueuedMessage(
            'Meeting',
            count($userIds),
            $notifyEmail,
            $notifySms,
            $notifyWhatsApp
        );

        return redirect()
            ->route('admin.meetings.invite', ['meeting' => $meeting, 'reminder' => 1])
            ->with('success', $success);
    }

    public function toggleDisplay(Meeting $meeting)
    {
        $meeting->update(['is_active' => ! $meeting->is_active]);

        return redirect()->route('admin.meetings.index')->with('success', 'Display status updated.');
    }

    public function inviteForm(Meeting $meeting, Request $request)
    {
        $this->syncElapsedMeetings();
        $meeting->refresh();

        $reminderMode = $request->boolean('reminder');
        if ($reminderMode) {
            if (! $meeting->is_active || ! in_array($meeting->status, ['upcoming', 'live'], true)) {
                return redirect()
                    ->route('admin.meetings.index')
                    ->with('error', 'Reminders are only available when the meeting display is Active and the status is Upcoming or Live.');
            }
        }

        $q = trim((string) $request->query('q', ''));
        $statusTab = (string) $request->query('status_tab', 'all');
        $allowedTabs = ['all', 'invited', 'interested', 'participated', 'not_participated'];
        if (! in_array($statusTab, $allowedTabs, true)) {
            $statusTab = 'all';
        }

        // Old rows may carry auto-default "interested" before member action.
        // Normalize those rows back to "invited" so interested count is accurate.
        MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->where('participation_status', 'interested')
            ->whereNull('attended_at')
            ->whereNotNull('invited_at')
            ->whereColumn('updated_at', '<=', 'invited_at')
            ->update(['participation_status' => 'invited']);

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

        $invitedUserIds = MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->pluck('user_id')
            ->all();

        $invites = MeetingInvite::query()
            ->with('user:id,name,email,mobile')
            ->where('meeting_id', $meeting->id)
            ->when($statusTab !== 'all', fn ($query) => $query->where('participation_status', $statusTab))
            ->latest('id')
            ->get();

        $statusCounts = MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN participation_status = 'invited' THEN 1 ELSE 0 END) as invited_count")
            ->selectRaw("SUM(CASE WHEN participation_status = 'interested' THEN 1 ELSE 0 END) as interested_count")
            ->selectRaw("SUM(CASE WHEN participation_status = 'participated' THEN 1 ELSE 0 END) as attended_count")
            ->selectRaw("SUM(CASE WHEN participation_status = 'not_participated' THEN 1 ELSE 0 END) as not_attended_count")
            ->first();

        return view('admin.meetings.invite', compact('meeting', 'members', 'invitedUserIds', 'invites', 'q', 'statusTab', 'statusCounts', 'reminderMode'));
    }

    public function inviteStore(Meeting $meeting, Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:approved,specific',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'select_all_approved' => 'nullable|boolean',
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

        $selectAllApproved = $request->boolean('select_all_approved');
        $sendToApproved = $validated['target'] === 'approved' || $selectAllApproved;

        $userIds = $sendToApproved
            ? User::query()->where('is_approved', true)->pluck('id')->all()
            : array_values(array_unique($validated['member_ids'] ?? []));

        if (empty($userIds)) {
            return back()->withErrors([
                'member_ids' => 'Please select at least one member.',
            ])->withInput();
        }

        $now = now();
        foreach ($userIds as $userId) {
            $invite = MeetingInvite::query()->firstOrNew([
                'meeting_id' => $meeting->id,
                'user_id' => $userId,
            ]);

            // Keep finalized responses; invite/re-invite should stay in "invited" state
            // until member explicitly chooses Interested.
            if (
                ! $invite->exists
                || empty($invite->participation_status)
                || (
                    in_array($invite->participation_status, ['invited', 'interested'], true)
                    && $invite->attended_at === null
                )
            ) {
                $invite->participation_status = 'invited';
            }

            $invite->notify_whatsapp = $notifyWhatsApp;
            $invite->notify_sms = $notifySms;
            $invite->notify_email = $notifyEmail;
            $invite->invited_at = $now;
            $invite->save();
        }

        $intUserIds = array_map('intval', $userIds);
        $chunkSize = 200;
        $batch = GnatNotificationBatch::start(
            auth('admin')->id(),
            SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITES,
            (int) $meeting->id,
            (string) $meeting->title,
            count($intUserIds),
            $chunkSize,
            [
                'notify_email' => $notifyEmail,
                'notify_sms' => $notifySms,
                'notify_whatsapp' => $notifyWhatsApp,
            ]
        );

        SendGnatBulkNotificationChunkJob::dispatchChunks(
            SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITES,
            (int) $meeting->id,
            $intUserIds,
            true,
            false,
            false,
            $chunkSize,
            $batch->id
        );

        return redirect()->route('admin.meetings.invite', $meeting->id)->with('success', 'Invitations have been queued and will be sent in the background.');
    }

    public function removeInvite(Meeting $meeting, MeetingInvite $invite)
    {
        if ($invite->meeting_id !== $meeting->id) {
            abort(404);
        }

        $invite->delete();

        return back()->with('success', 'Member removed from invite list.');
    }

    public function updateInviteAttendance(Request $request, Meeting $meeting, MeetingInvite $invite)
    {
        if ($invite->meeting_id !== $meeting->id) {
            abort(404);
        }

        if ($meeting->status === 'cancelled') {
            return back()->with('error', 'Attendance cannot be updated for a cancelled meeting.');
        }

        if (! in_array($meeting->status, ['live', 'completed'], true)) {
            return back()->with('error', 'Attendance can be updated only when the meeting is Live or Completed.');
        }

        $validated = $request->validate([
            'participation_status' => 'required|in:invited,interested,participated,not_participated',
        ]);

        $invite->update([
            'participation_status' => $validated['participation_status'],
            'attended_at' => $validated['participation_status'] === 'participated' ? now() : null,
        ]);

        return back()->with('success', 'Meeting attendance updated.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'meeting_link' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meeting_mode' => 'required|in:whatsapp,teams,others,direct,phone_call',
            'status' => 'required|in:upcoming,live,completed,cancelled',
            'is_active' => 'nullable|boolean',
            'cover_image' => 'nullable|image|max:5120',
            'banner_image' => 'nullable|image|max:5120',
            'schedule_date' => 'required|date',
            'schedule_from' => 'required|date_format:H:i',
            'schedule_to' => 'required|date_format:H:i|after:schedule_from',
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating): array
    {
        $payload = [
            'title' => $validated['title'],
            'meeting_link' => isset($validated['meeting_link']) && $validated['meeting_link'] !== ''
                ? $validated['meeting_link']
                : null,
            'description' => $validated['description'] ?? null,
            'meeting_mode' => $validated['meeting_mode'],
            'status' => $validated['status'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $request->file('cover_image')->store('meetings/covers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $payload['banner_image_path'] = $request->file('banner_image')->store('meetings/banners', 'public');
        }

        return $payload;
    }

    private function extractSchedule(Request $request): array
    {
        return [
            'meeting_date' => $request->input('schedule_date'),
            'from_time' => $request->input('schedule_from'),
            'to_time' => $request->input('schedule_to'),
        ];
    }

    private function mergeNormalizedScheduleTimes(Request $request): void
    {
        $request->merge([
            'schedule_from' => $this->normalizeHiTime($request->input('schedule_from')),
            'schedule_to' => $this->normalizeHiTime($request->input('schedule_to')),
        ]);
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

    private function syncElapsedMeetings(): void
    {
        $activeMeetings = Meeting::query()
            ->whereIn('status', ['upcoming', 'live'])
            ->with(['schedules:id,meeting_id,meeting_date,from_time,to_time'])
            ->get(['id', 'status', 'is_active']);

        $now = now();
        foreach ($activeMeetings as $meeting) {
            $schedule = $meeting->schedules->sortBy('meeting_date')->first();
            $lastSchedule = $meeting->schedules->sortBy('meeting_date')->last();
            if (! $schedule?->meeting_date || ! $schedule?->from_time || ! $lastSchedule?->meeting_date || ! $lastSchedule?->to_time) {
                continue;
            }

            $start = Carbon::parse($schedule->meeting_date->format('Y-m-d').' '.$schedule->from_time);
            $end = Carbon::parse($lastSchedule->meeting_date->format('Y-m-d').' '.$lastSchedule->to_time);

            if ($now->greaterThan($end)) {
                $meeting->update([
                    'status' => 'completed',
                    'is_active' => false,
                ]);
                continue;
            }

            if ($now->greaterThanOrEqualTo($start) && $meeting->status !== 'live') {
                $meeting->update(['status' => 'live']);
                continue;
            }

            if ($now->lt($start) && $meeting->status !== 'upcoming') {
                $meeting->update(['status' => 'upcoming']);
            }
        }
    }

    /**
     * Invited members (have been sent an invite at least once).
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\MeetingInvite>
     */
    private function meetingReminderEligibleInvitesQuery(Meeting $meeting)
    {
        return MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->whereNotNull('invited_at');
    }

    private function gateMeetingReminder(Meeting $meeting): ?\Illuminate\Http\RedirectResponse
    {
        $this->syncElapsedMeetings();
        $meeting->refresh();

        if (! $meeting->is_active) {
            return redirect()
                ->route('admin.meetings.invite', $meeting)
                ->with('error', 'Reminders can only be sent when the meeting display status is Active.');
        }

        if (! in_array($meeting->status, ['upcoming', 'live'], true)) {
            return redirect()
                ->route('admin.meetings.invite', $meeting)
                ->with('error', 'Reminders can only be sent for meetings in Upcoming or Live status.');
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
}
