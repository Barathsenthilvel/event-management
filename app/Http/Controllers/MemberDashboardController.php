<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInvite;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profileIncomplete = !$user?->profile_completed
            || empty($user?->first_name)
            || empty($user?->last_name)
            || empty($user?->mobile)
            || empty($user?->dob)
            || empty($user?->gender)
            || empty($user?->qualification)
            || empty($user?->blood_group)
            || empty($user?->rnrm_number_with_date)
            || empty($user?->college_name)
            || empty($user?->door_no)
            || empty($user?->locality_area)
            || empty($user?->state)
            || empty($user?->pin_code)
            || empty($user?->council_state)
            || empty($user?->educational_certificate_path)
            || empty($user?->aadhar_card_path)
            || empty($user?->passport_photo_path);

        $events = Event::query()
            ->with(['dates:id,event_id,event_date,start_time,end_time'])
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live', 'completed'])
            ->latest('id')
            ->get();

        $interestedEventIds = EventInvite::query()
            ->where('user_id', $user?->id)
            ->pluck('event_id')
            ->all();

        return view('member.dashboard', [
            'profileIncomplete' => $profileIncomplete,
            'activeSubscription' => $user?->activeSubscription,
            'events' => $events,
            'interestedEventIds' => $interestedEventIds,
            'transactions' => PaymentTransaction::query()
                ->with('subscriptionPlan')
                ->where('user_id', $user?->id)
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function submitInterest(Request $request, Event $event)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if (!$event->is_active || $event->status === 'cancelled') {
            return back()->with('event_interest_error', 'This event is not available for interest now.');
        }

        $alreadyInterested = EventInvite::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyInterested) {
            return back()->with('event_interest_error', 'You have already submitted interest for this event.');
        }

        try {
            DB::transaction(function () use ($event, $user) {
                $event->refresh();
                if ($event->seat_mode === 'limited' && $event->seat_limit !== null && $event->interested_count >= $event->seat_limit) {
                    throw new \RuntimeException('Seat limit reached for this event.');
                }

                EventInvite::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'participation_status' => 'interested',
                    'invited_at' => now(),
                ]);

                $event->update([
                    'interested_count' => (int) $event->interested_count + 1,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('event_interest_error', $e->getMessage());
        } catch (QueryException $e) {
            return back()->with('event_interest_error', 'You have already submitted interest for this event.');
        }

        return back()->with('event_interest_success', 'Interest submitted successfully.');
    }
}

