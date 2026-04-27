<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventInterestController extends Controller
{
    public function store(Request $request, Event $event)
    {
        if (!$event->is_active || $event->status === 'cancelled') {
            return back()
                ->with('event_interest_error', 'This event is not accepting interest right now.')
                ->with('event_interest_error_modal', true);
        }

        if (Auth::check()) {
            if (EventInterest::query()->where('event_id', $event->id)->where('user_id', Auth::id())->exists()) {
                return back()->with('info', 'You have already registered interest in this event.');
            }
            $request->merge(['email' => Auth::user()->email]);
        }

        $emailRules = ['required', 'email', 'max:255'];
        if (Auth::check()) {
            $emailRules[] = Rule::in([Auth::user()->email]);
        } else {
            $emailRules[] = Rule::unique('event_interests', 'email')->where(
                fn ($q) => $q->where('event_id', $event->id)
            );
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'phone' => ['required', 'string', 'max:64'],
        ]);

        $email = strtolower(trim($data['email']));

        DB::transaction(function () use ($event, $data, $email, $request) {
            EventInterest::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(),
                'name' => $data['name'],
                'email' => $email,
                'phone' => $data['phone'],
            ]);
            $event->increment('interested_count');
        });

        if (!Auth::check()) {
            $request->session()->push('guest_event_interests', $event->id);
        }

        return back()
            ->with('event_interest_success', 'Thank you. Your event interest has been recorded.')
            ->with('event_interest_success_modal', true);
    }
}
