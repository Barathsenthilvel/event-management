<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event;
use App\Models\EventInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Public marketing homepage (GNAT Donation).
     * Content is static via config('homepage') until wired to the database.
     */
    public function index()
    {
        $config = config('homepage', []);

        $homeEvents = Event::query()
            ->with(['dates:id,event_id,event_date,start_time,end_time', 'creator:id,name'])
            ->withCount('invites')
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->latest('id')
            ->limit(8)
            ->get();

        $interestedEventIds = [];
        if (Auth::check() && $homeEvents->isNotEmpty()) {
            $interestedEventIds = EventInterest::query()
                ->where('user_id', Auth::id())
                ->whereIn('event_id', $homeEvents->pluck('id'))
                ->pluck('event_id')
                ->all();
        }

        $guestInterestedEventIds = collect(session('guest_event_interests', []))
            ->unique()
            ->values()
            ->all();

        return view(
            'home.index',
            array_merge($config, compact('homeEvents', 'interestedEventIds', 'guestInterestedEventIds'))
        );
    }

    public function events(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));
        $allowedStatuses = ['all', 'upcoming', 'live', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $events = Event::query()
            ->with(['dates:id,event_id,event_date,start_time,end_time', 'creator:id,name'])
            ->withCount('invites')
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%' . $q . '%')
                        ->orWhere('description', 'like', '%' . $q . '%')
                        ->orWhere('venue', 'like', '%' . $q . '%');
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(24)
            ->withQueryString();

        $interestedEventIds = [];
        if (Auth::check() && $events->count() > 0) {
            $interestedEventIds = EventInterest::query()
                ->where('user_id', Auth::id())
                ->whereIn('event_id', $events->pluck('id'))
                ->pluck('event_id')
                ->all();
        }

        $guestInterestedEventIds = collect(session('guest_event_interests', []))
            ->unique()
            ->values()
            ->all();

        return view(
            'home.events',
            array_merge(config('homepage', []), compact('events', 'q', 'status', 'interestedEventIds', 'guestInterestedEventIds'))
        );
    }

    /**
     * Public donations listing (same campaigns as the marketing site; open to guests and members).
     */
    public function donations(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $donations = Donation::query()
            ->with('creator:id,name')
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('purpose', 'like', '%' . $q . '%')
                        ->orWhere('short_description', 'like', '%' . $q . '%')
                        ->orWhere('description', 'like', '%' . $q . '%');
                });
            })
            ->orderByDesc('promote_front')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view(
            'home.donations',
            array_merge(config('homepage', []), compact('donations', 'q'))
        );
    }
}
