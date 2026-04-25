<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\HomeBlogPost;
use App\Models\HomeBanner;
use App\Models\HomeGalleryItem;
use App\Models\HomeGallerySection;
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
        $dbBanners = HomeBanner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest('id')
            ->get();

        $banners = $dbBanners->isNotEmpty()
            ? $dbBanners->map(function (HomeBanner $banner) {
                return [
                    'href' => $banner->link_url ?: '#',
                    'src' => 'storage/' . ltrim((string) $banner->image_path, '/'),
                    'alt' => $banner->alt_text ?: ($banner->title ?: 'Homepage banner'),
                    'eyebrow' => $banner->eyebrow,
                    'title' => $banner->caption_title ?: $banner->title,
                    'text' => $banner->caption_text,
                ];
            })->values()->all()
            : ($config['banners'] ?? []);

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
            $ids = $homeEvents->pluck('id');
            $interestedEventIds = EventInvite::query()
                ->where('user_id', Auth::id())
                ->whereIn('event_id', $ids)
                ->pluck('event_id')
                ->merge(
                    EventInterest::query()
                        ->where('user_id', Auth::id())
                        ->whereIn('event_id', $ids)
                        ->pluck('event_id')
                )
                ->unique()
                ->values()
                ->all();
        }

        $guestInterestedEventIds = collect(session('guest_event_interests', []))
            ->unique()
            ->values()
            ->all();

        $homeDonations = Donation::query()
            ->where('is_active', true)
            ->orderByDesc('promote_front')
            ->latest('id')
            ->limit(12)
            ->get();

        return view(
            'home.index',
            array_merge($config, compact('homeEvents', 'interestedEventIds', 'guestInterestedEventIds', 'homeDonations', 'banners'))
        );
    }

    public function blogs(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $posts = HomeBlogPost::query()
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('tag', 'like', '%'.$q.'%')
                        ->orWhere('excerpt', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view(
            'home.blogs',
            array_merge(config('homepage', []), compact('posts', 'q'))
        );
    }

    public function gallery(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', 'all'));

        $allowedCategories = ['all', 'programs', 'events', 'community'];
        if (! in_array($category, $allowedCategories, true)) {
            $category = 'all';
        }

        $items = HomeGalleryItem::query()
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('eyebrow', 'like', '%'.$q.'%')
                        ->orWhere('description_text', 'like', '%'.$q.'%');
                });
            })
            ->when($category !== 'all', fn ($query) => $query->where('category_key', $category))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $section = HomeGallerySection::query()->first();

        return view(
            'home.gallery',
            array_merge(config('homepage', []), compact('items', 'q', 'category', 'section'))
        );
    }

    public function events(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));
        $allowedStatuses = ['all', 'upcoming', 'live'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $events = Event::query()
            ->with(['dates:id,event_id,event_date,start_time,end_time', 'creator:id,name'])
            ->withCount('invites')
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhere('venue', 'like', '%'.$q.'%');
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(24)
            ->withQueryString();

        $interestedEventIds = [];
        if (Auth::check() && $events->count() > 0) {
            $ids = $events->pluck('id');
            $interestedEventIds = EventInvite::query()
                ->where('user_id', Auth::id())
                ->whereIn('event_id', $ids)
                ->pluck('event_id')
                ->merge(
                    EventInterest::query()
                        ->where('user_id', Auth::id())
                        ->whereIn('event_id', $ids)
                        ->pluck('event_id')
                )
                ->unique()
                ->values()
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
                    $sub->where('purpose', 'like', '%'.$q.'%')
                        ->orWhere('short_description', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%');
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
