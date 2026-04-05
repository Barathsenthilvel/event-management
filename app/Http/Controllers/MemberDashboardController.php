<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInvite;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

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

        $myEventInvites = EventInvite::query()
            ->where('user_id', $user?->id)
            ->with(['event.dates:id,event_id,event_date,start_time,end_time'])
            ->latest('id')
            ->get();

        $latestReceiptTransaction = PaymentTransaction::query()
            ->with('subscriptionPlan')
            ->where('user_id', $user?->id)
            ->where('status', 'successful')
            ->latest('id')
            ->first();

        return view('member.dashboard', [
            'activeSubscription' => $user?->activeSubscription,
            'events' => $events,
            'interestedEventIds' => $interestedEventIds,
            'myEventInvites' => $myEventInvites,
            'inviteByEventId' => $myEventInvites->keyBy('event_id'),
            'latestReceiptTransaction' => $latestReceiptTransaction,
            'transactions' => PaymentTransaction::query()
                ->with('subscriptionPlan')
                ->where('user_id', $user?->id)
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function downloadEventCertificate(Event $event)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $invite = EventInvite::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$invite || $invite->participation_status !== 'participated') {
            return redirect()->route('member.dashboard')->with(
                'event_interest_error',
                'Your certificate is available only after the admin marks you as participated for this event.'
            );
        }

        $event->loadMissing('dates');

        if (empty($event->template_pdf_path) || !Storage::disk('public')->exists($event->template_pdf_path)) {
            return redirect()->route('member.dashboard')->with(
                'event_interest_error',
                'Certificate file is not available for this event yet. Please contact the office.'
            );
        }

        $memberName = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) ($user->name ?? 'member'));
        $eventTitle = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $event->title);
        $fileName = trim("{$eventTitle}-{$memberName}-certificate.pdf", '-');

        return response()->download(
            Storage::disk('public')->path($event->template_pdf_path),
            $fileName
        );
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

