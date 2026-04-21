<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Meeting;
use App\Models\MeetingInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
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
                'schedule_from' => $schedule?->from_time,
                'schedule_to' => $schedule?->to_time,
            ],
            'duplicateSourceMeeting' => $meeting,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($request, $validated) {
            $meeting = Meeting::create($this->buildPayload($request, $validated, true));
            $schedule = $this->extractSchedule($request);
            $meeting->schedules()->create($schedule);

            if ($request->boolean('repeat_enabled')) {
                $repeatCount = (int) ($validated['repeat_count'] ?? 0);
                $repeatFrequency = (string) ($validated['repeat_frequency'] ?? 'weekly');
                $this->createRepeatedMeetings($meeting, $schedule, $repeatCount, $repeatFrequency);
            }
        });

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting created successfully.');
    }

    public function edit(Meeting $meeting)
    {
        $meeting->load('schedules');

        return view('admin.meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
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

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting cancelled.');
    }

    public function sendReminder(Meeting $meeting)
    {
        MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->update(['reminder_sent_at' => now()]);

        return redirect()->route('admin.meetings.index')->with('success', 'Reminder sent (marked) successfully.');
    }

    public function toggleDisplay(Meeting $meeting)
    {
        $meeting->update(['is_active' => ! $meeting->is_active]);

        return redirect()->route('admin.meetings.index')->with('success', 'Display status updated.');
    }

    public function inviteForm(Meeting $meeting, Request $request)
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

        $invitedUserIds = MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->pluck('user_id')
            ->all();

        $invites = MeetingInvite::query()
            ->with('user:id,name,email,mobile')
            ->where('meeting_id', $meeting->id)
            ->latest('id')
            ->get();

        return view('admin.meetings.invite', compact('meeting', 'members', 'invitedUserIds', 'invites', 'q'));
    }

    public function inviteStore(Meeting $meeting, Request $request)
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
            MeetingInvite::updateOrCreate(
                ['meeting_id' => $meeting->id, 'user_id' => $userId],
                [
                    'notify_whatsapp' => $notifyWhatsApp,
                    'notify_sms' => $notifySms,
                    'notify_email' => $notifyEmail,
                    'invited_at' => $now,
                ]
            );
        }

        return redirect()->route('admin.meetings.invite', $meeting->id)->with('success', 'Members invited successfully.');
    }

    public function removeInvite(Meeting $meeting, MeetingInvite $invite)
    {
        if ($invite->meeting_id !== $meeting->id) {
            abort(404);
        }

        $invite->delete();

        return back()->with('success', 'Member removed from invite list.');
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
            'repeat_enabled' => 'nullable|boolean',
            'repeat_frequency' => 'nullable|required_if:repeat_enabled,1|in:weekly,monthly',
            'repeat_count' => 'nullable|required_if:repeat_enabled,1|integer|min:1|max:24',
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

    private function createRepeatedMeetings(Meeting $meeting, array $baseSchedule, int $repeatCount, string $frequency): void
    {
        if ($repeatCount < 1) {
            return;
        }

        $scheduleDate = Carbon::parse((string) $baseSchedule['meeting_date']);
        for ($i = 1; $i <= $repeatCount; $i++) {
            $nextDate = (clone $scheduleDate);
            if ($frequency === 'monthly') {
                $nextDate->addMonthsNoOverflow($i);
            } else {
                $nextDate->addWeeks($i);
            }

            $copy = Meeting::create([
                'created_by_admin_id' => $meeting->created_by_admin_id,
                'title' => $meeting->title,
                'meeting_link' => $meeting->meeting_link,
                'description' => $meeting->description,
                'meeting_mode' => $meeting->meeting_mode,
                'status' => $meeting->status,
                'cover_image_path' => $meeting->cover_image_path,
                'banner_image_path' => $meeting->banner_image_path,
                'is_active' => $meeting->is_active,
            ]);

            $copy->schedules()->create([
                'meeting_date' => $nextDate->format('Y-m-d'),
                'from_time' => $baseSchedule['from_time'],
                'to_time' => $baseSchedule['to_time'],
            ]);
        }
    }
}
