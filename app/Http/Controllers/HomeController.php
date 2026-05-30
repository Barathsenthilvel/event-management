<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\HomeBanner;
use App\Models\HomeBlogPost;
use App\Models\HomeBlogSection;
use App\Models\HomeGalleryItem;
use App\Models\HomeGallerySection;
use App\Services\EventScheduleStatusService;
use App\Services\GnatMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Public marketing homepage (GNAT Donation).
     * Banners, blog, and gallery sections load from the database when available; other blocks may still use config('homepage') fallbacks.
     */
    public function index()
    {
        if (Schema::hasTable('events')) {
            resolve(EventScheduleStatusService::class)->syncAll();
        }

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
                    'src' => 'storage/'.ltrim((string) $banner->image_path, '/'),
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
            ->where('promote_front', true)
            ->whereIn('status', ['upcoming', 'live', 'completed'])
            ->orderByRaw("CASE status WHEN 'live' THEN 0 WHEN 'upcoming' THEN 1 WHEN 'completed' THEN 2 ELSE 3 END")
            ->latest('id')
            ->limit(8)
            ->get();

        $interestedEventIds = $this->resolveInterestedEventIdsForHome($homeEvents);
        $guestInterestedEventIds = $this->resolveGuestInterestedEventIds();

        $homeDonations = Donation::query()
            ->where('is_active', true)
            ->orderByDesc('promote_front')
            ->latest('id')
            ->limit(12)
            ->get();

        $blog = $this->resolveHomepageBlog();
        $gallery = $this->resolveHomepageGallery();

        return view(
            'home.index',
            array_merge($config, compact('homeEvents', 'interestedEventIds', 'guestInterestedEventIds', 'homeDonations', 'banners', 'blog', 'gallery'))
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

        $section = Schema::hasTable('home_blog_sections')
            ? HomeBlogSection::query()->first()
            : null;

        return view(
            'home.blogs',
            array_merge(config('homepage', []), compact('posts', 'q', 'section'))
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

        $galleryItems = $this->resolveGalleryPageItems($category, $q);
        $section = Schema::hasTable('home_gallery_sections')
            ? HomeGallerySection::query()->first()
            : null;

        return view(
            'home.gallery',
            array_merge(config('homepage', []), compact('galleryItems', 'q', 'category', 'section'))
        );
    }

    public function events(Request $request)
    {
        if (Schema::hasTable('events')) {
            resolve(EventScheduleStatusService::class)->syncAll();
        }

        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));
        $allowedStatuses = ['all', 'upcoming', 'live', 'completed'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $events = Event::query()
            ->with(['dates:id,event_id,event_date,start_time,end_time', 'creator:id,name'])
            ->withCount('invites')
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live', 'completed'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhere('venue', 'like', '%'.$q.'%');
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderByRaw("CASE status WHEN 'live' THEN 0 WHEN 'upcoming' THEN 1 WHEN 'completed' THEN 2 ELSE 3 END")
            ->latest('id')
            ->paginate(24)
            ->withQueryString();

        $interestedEventIds = $this->resolveInterestedEventIdsForHome($events);
        $guestInterestedEventIds = $this->resolveGuestInterestedEventIds();

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

    public function activities()
    {
        $activities = config('homepage.activities', []);
        $items = $activities['items'] ?? [];

        return view(
            'home.activities',
            array_merge(config('homepage', []), compact('activities', 'items'))
        );
    }

    public function activityShow(string $slug)
    {
        $item = $this->resolveActivityItem($slug);

        if ($item === null) {
            abort(404);
        }

        $activities = config('homepage.activities', []);
        $items = collect($activities['items'] ?? [])->values();
        $index = $items->search(fn (array $row) => ($row['slug'] ?? '') === $slug);
        $prev = $index !== false && $index > 0 ? $items[$index - 1] : null;
        $next = $index !== false && $index < $items->count() - 1 ? $items[$index + 1] : null;

        return view(
            'home.activity-show',
            array_merge(config('homepage', []), compact('activities', 'item', 'prev', 'next'))
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveActivityItem(string $slug): ?array
    {
        $items = config('homepage.activities.items', []);

        foreach ($items as $item) {
            if (($item['slug'] ?? '') === $slug) {
                return $item;
            }
        }

        return null;
    }

    public function about()
    {
        return view('home.about', config('homepage', []));
    }

    public function contact()
    {
        return view('home.contact', config('homepage', []));
    }

    public function privacyPolicy()
    {
        return view('home.legal.privacy', $this->legalPageData());
    }

    public function termsAndConditions()
    {
        return view('home.legal.terms', $this->legalPageData());
    }

    public function disclaimer()
    {
        return view('home.legal.disclaimer', $this->legalPageData(showEffectiveDate: false));
    }

    public function cancellationRefundPolicy()
    {
        return view('home.legal.cancellation-refund', $this->legalPageData(showEffectiveDate: false));
    }

    /**
     * @return array{section_badge: string, section_title: string, section_description: string, filters: list<array{key: string, label: string}>, items: \Illuminate\Support\Collection<int, HomeGalleryItem>|\Illuminate\Support\Collection<int, array<string, mixed>>}
     */
    private function resolveHomepageGallery(): array
    {
        $defaults = config('homepage.gallery', []);
        $section = Schema::hasTable('home_gallery_sections')
            ? HomeGallerySection::query()->first()
            : null;

        $this->ensureGalleryCategoryPrimaries();

        $dbItems = Schema::hasTable('home_gallery_items')
            ? HomeGalleryItem::query()
                ->where('is_active', true)
                ->orderByDesc('is_category_primary')
                ->orderBy('sort_order')
                ->latest('id')
                ->get()
            : collect();

        $items = Schema::hasTable('home_gallery_items')
            ? $this->groupDbGalleryItemsIntoAlbums($dbItems)
            : collect($defaults['items'] ?? []);

        $items = $items->concat($this->buildEventGalleryAlbums());
        $items = $this->limitHomepageGalleryItems($items);

        return [
            'section_badge' => $section?->section_badge ?? 'Impact in pictures',
            'section_title' => $section?->section_title ?? 'Our gallery',
            'section_description' => $section?->section_description ?? 'Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.',
            'filters' => $defaults['filters'] ?? [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'programs', 'label' => 'Programs'],
                ['key' => 'events', 'label' => 'Events'],
                ['key' => 'community', 'label' => 'Community'],
            ],
            'items' => $items,
        ];
    }

    private function ensureGalleryCategoryPrimaries(): void
    {
        if (! Schema::hasTable('home_gallery_items') || ! Schema::hasColumn('home_gallery_items', 'is_category_primary')) {
            return;
        }

        foreach (['programs', 'events', 'community'] as $categoryKey) {
            $hasPrimary = HomeGalleryItem::query()
                ->where('category_key', $categoryKey)
                ->where('is_active', true)
                ->where('is_category_primary', true)
                ->exists();

            if ($hasPrimary) {
                continue;
            }

            $first = HomeGalleryItem::query()
                ->where('category_key', $categoryKey)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if ($first) {
                $first->update(['is_category_primary' => true]);
            }
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildEventGalleryAlbums(string $search = ''): Collection
    {
        if (! Schema::hasTable('events')) {
            return collect();
        }

        $events = Event::query()
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live', 'completed'])
            ->with(['photos' => fn ($q) => $q->orderBy('id')])
            ->orderByDesc('promote_front')
            ->latest('id')
            ->get();

        $albums = collect();
        $search = trim($search);

        foreach ($events as $event) {
            $paths = collect();

            if (filled($event->cover_image_path)) {
                $paths->push(ltrim((string) $event->cover_image_path, '/'));
            }
            if (filled($event->banner_image_path)) {
                $paths->push(ltrim((string) $event->banner_image_path, '/'));
            }
            foreach ($event->photos as $photo) {
                if (filled($photo->photo_path)) {
                    $paths->push(ltrim((string) $photo->photo_path, '/'));
                }
            }

            $paths = $paths->filter()->unique()->values();
            if ($paths->isEmpty()) {
                continue;
            }

            $description = Str::limit(strip_tags((string) ($event->description ?? '')), 120);
            $title = (string) $event->title;

            if ($search !== '' && ! str_contains(strtolower($title), strtolower($search)) && ! str_contains(strtolower($description), strtolower($search))) {
                continue;
            }

            $albumImages = $paths->map(fn (string $path) => [
                'src' => asset('storage/'.$path),
                'title' => $title,
                'cat' => 'Events',
            ])->values()->all();

            $coverPath = $paths->first();

            $albums->push([
                'cat' => 'events',
                'layout' => 'wide',
                'image' => 'storage/'.$coverPath,
                'alt' => $title,
                'eyebrow' => 'Events',
                'title' => $title,
                'text' => $description,
                'is_category_primary' => false,
                'from_event' => true,
                'sort_order' => 900000 + (int) $event->id,
                'uploaded_at' => (int) ($event->created_at?->timestamp ?? $event->id),
                'photo_count' => count($albumImages),
                'album_images' => $albumImages,
            ]);
        }

        return $albums->values();
    }

    /**
     * All gallery albums for the dedicated gallery page (grouped by upload batch / album).
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function resolveGalleryPageItems(string $category, string $search = ''): Collection
    {
        $dbItems = Schema::hasTable('home_gallery_items')
            ? HomeGalleryItem::query()
                ->where('is_active', true)
                ->orderByDesc('is_category_primary')
                ->orderBy('sort_order')
                ->latest('id')
                ->get()
            : collect();

        $albums = $this->groupDbGalleryItemsIntoAlbums($dbItems);

        if (in_array($category, ['all', 'events'], true)) {
            $albums = $albums->concat($this->buildEventGalleryAlbums($search));
        }

        $search = trim($search);

        return $albums
            ->filter(function (array $album) use ($category, $search) {
                if ($category !== 'all' && ($album['cat'] ?? '') !== $category) {
                    return false;
                }

                if ($search === '') {
                    return true;
                }

                $haystack = strtolower(implode(' ', array_filter([
                    $album['title'] ?? '',
                    $album['text'] ?? '',
                    $album['eyebrow'] ?? '',
                ])));

                return str_contains($haystack, strtolower($search));
            })
            ->values();
    }

    /**
     * @param  Collection<int, HomeGalleryItem>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function groupDbGalleryItemsIntoAlbums(Collection $items): Collection
    {
        return $items
            ->groupBy(fn (HomeGalleryItem $item) => $this->galleryAlbumGroupKey($item))
            ->map(function (Collection $group) {
                $sorted = $group->sortBy([
                    fn (HomeGalleryItem $item) => $item->is_category_primary ? 0 : 1,
                    fn (HomeGalleryItem $item) => $item->sort_order,
                    fn (HomeGalleryItem $item) => $item->id,
                ])->values();

                /** @var HomeGalleryItem $cover */
                $cover = $sorted->first();
                $title = $this->normalizeGalleryAlbumTitle((string) $cover->title);

                $albumImages = $sorted->map(fn (HomeGalleryItem $item) => [
                    'src' => asset('storage/'.ltrim((string) $item->image_path, '/')),
                    'title' => $this->normalizeGalleryAlbumTitle((string) $item->title),
                    'cat' => $item->eyebrow ?: ucfirst((string) $item->category_key),
                ])->values()->all();

                return [
                    'cat' => $cover->category_key,
                    'layout' => 'cell',
                    'image' => 'storage/'.ltrim((string) $cover->image_path, '/'),
                    'alt' => $cover->alt_text ?: $title,
                    'eyebrow' => $cover->eyebrow ?: ucfirst((string) $cover->category_key),
                    'title' => $title,
                    'text' => $cover->description_text,
                    'is_category_primary' => (bool) $cover->is_category_primary,
                    'from_event' => false,
                    'sort_order' => (int) $cover->sort_order,
                    'uploaded_at' => (int) $sorted->max(fn (HomeGalleryItem $item) => $item->created_at?->timestamp ?? $item->id),
                    'photo_count' => $sorted->count(),
                    'album_images' => $albumImages,
                ];
            })
            ->sortBy(fn (array $album) => [
                $album['is_category_primary'] ? 0 : 1,
                $album['sort_order'],
            ])
            ->values();
    }

    private function galleryAlbumGroupKey(HomeGalleryItem $item): string
    {
        if (Schema::hasColumn('home_gallery_items', 'upload_batch_id') && filled($item->upload_batch_id)) {
            return 'batch:'.$item->upload_batch_id;
        }

        $title = mb_strtolower(trim($this->normalizeGalleryAlbumTitle((string) $item->title)));
        $minute = $item->created_at?->format('Y-m-d H:i') ?? '';

        return 'legacy:'.$item->category_key.'|'.$title.'|'.$minute;
    }

    private function normalizeGalleryAlbumTitle(string $title): string
    {
        $normalized = preg_replace('/\s\(\d+\)$/', '', trim($title));

        return $normalized !== '' ? $normalized : $title;
    }

    /**
     * Homepage gallery preview limits: Programs 2, Events 1, Community 1 (4 total on ALL).
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function limitHomepageGalleryItems(Collection $items): Collection
    {
        $categoryConfig = [
            'programs' => ['limit' => 2, 'layouts' => ['hero', 'cell'], 'slots' => ['hero', 'programs-cell']],
            'events' => ['limit' => 1, 'layouts' => ['wide'], 'slots' => ['events-wide']],
            'community' => ['limit' => 1, 'layouts' => ['cell'], 'slots' => ['community-cell']],
        ];

        $byCategory = [];

        foreach ($categoryConfig as $categoryKey => $config) {
            $byCategory[$categoryKey] = $items
                ->filter(fn ($item) => $this->galleryItemCategory($item) === $categoryKey)
                ->sortBy(fn ($item) => [
                    $this->galleryItemIsPrimary($item) ? 0 : 1,
                    $this->galleryItemSortOrder($item),
                ])
                ->values()
                ->take($config['limit'])
                ->values()
                ->map(fn ($item, $index) => $this->normalizeHomepageGalleryItem(
                    $item,
                    $config['layouts'][$index] ?? 'cell',
                    $config['slots'][$index] ?? null
                ));
        }

        return collect([
            $byCategory['programs'][0] ?? null,
            $byCategory['programs'][1] ?? null,
            $byCategory['community'][0] ?? null,
            $byCategory['events'][0] ?? null,
        ])->filter()->values();
    }

    private function galleryItemCategory(mixed $item): string
    {
        if (is_object($item) && method_exists($item, 'getAttribute')) {
            return (string) $item->category_key;
        }

        return (string) ($item['cat'] ?? 'programs');
    }

    private function galleryItemIsPrimary(mixed $item): bool
    {
        if (is_object($item) && method_exists($item, 'getAttribute')) {
            return (bool) $item->is_category_primary;
        }

        return (bool) ($item['is_category_primary'] ?? false);
    }

    private function galleryItemSortOrder(mixed $item): int
    {
        if (is_object($item) && method_exists($item, 'getAttribute')) {
            return (int) $item->sort_order;
        }

        return (int) ($item['sort_order'] ?? 0);
    }

    private function galleryItemUploadedAt(mixed $item): int
    {
        if (is_array($item)) {
            return (int) ($item['uploaded_at'] ?? $item['sort_order'] ?? 0);
        }

        if (is_object($item) && method_exists($item, 'getAttribute')) {
            return (int) ($item->created_at?->timestamp ?? $item->id);
        }

        return 0;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeHomepageGalleryItem(mixed $item, string $layout, ?string $gridSlot = null): array
    {
        if (is_array($item)) {
            $normalized = $item;
            $normalized['layout'] = $layout;
            if ($gridSlot !== null) {
                $normalized['grid_slot'] = $gridSlot;
            }

            return $normalized;
        }

        if (is_object($item) && method_exists($item, 'getAttribute')) {
            $title = $this->normalizeGalleryAlbumTitle((string) $item->title);

            return [
                'cat' => $item->category_key,
                'layout' => $layout,
                'grid_slot' => $gridSlot,
                'image' => 'storage/'.ltrim((string) $item->image_path, '/'),
                'alt' => $item->alt_text ?: $title,
                'eyebrow' => $item->eyebrow ?: ucfirst((string) $item->category_key),
                'title' => $title,
                'text' => $item->description_text,
                'is_category_primary' => (bool) $item->is_category_primary,
                'from_event' => false,
                'sort_order' => (int) $item->sort_order,
                'uploaded_at' => (int) ($item->created_at?->timestamp ?? $item->id),
                'photo_count' => 1,
                'album_images' => [[
                    'src' => asset('storage/'.ltrim((string) $item->image_path, '/')),
                    'title' => $title,
                    'cat' => $item->eyebrow ?: ucfirst((string) $item->category_key),
                ]],
            ];
        }

        $normalized = (array) $item;
        $normalized['layout'] = $layout;
        if ($gridSlot !== null) {
            $normalized['grid_slot'] = $gridSlot;
        }

        return $normalized;
    }

    /**
     * @return array{section_badge: string, section_title: string, section_description: string, section_button_text: string, posts: \Illuminate\Support\Collection<int, HomeBlogPost>|\Illuminate\Support\Collection<int, array<string, mixed>>}
     */
    private function resolveHomepageBlog(): array
    {
        $defaults = config('homepage.blog', []);
        $section = Schema::hasTable('home_blog_sections')
            ? HomeBlogSection::query()->first()
            : null;

        $posts = Schema::hasTable('home_blog_posts')
            ? HomeBlogPost::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->latest('id')
                ->limit(12)
                ->get()
            : collect($defaults['posts'] ?? []);

        return [
            'section_badge' => $section?->section_badge ?? 'Our blog',
            'section_title' => $section?->section_title ?? 'Insights & Updates',
            'section_description' => $section?->section_description ?? 'Stay informed with the latest news, stories, and updates from GNAT Association. Explore ideas and initiatives shaping our communities.',
            'section_button_text' => $section?->section_button_text ?? 'Explore All Posts',
            'posts' => $posts,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Event>|iterable<int, Event>  $events
     * @return list<int>
     */
    private function resolveInterestedEventIdsForHome(iterable $events): array
    {
        if (! Auth::check()) {
            return [];
        }

        $ids = collect($events)->pluck('id')->map(fn ($id) => (int) $id)->filter()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        return EventInvite::query()
            ->where('user_id', Auth::id())
            ->whereIn('event_id', $ids)
            ->where('has_confirmed_interest', true)
            ->pluck('event_id')
            ->merge(
                EventInterest::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('event_id', $ids)
                    ->pluck('event_id')
            )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return list<int>
     */
    private function resolveGuestInterestedEventIds(): array
    {
        return collect(session('guest_event_interests', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function legalPageData(bool $showEffectiveDate = true): array
    {
        return array_merge(config('homepage', []), [
            'effectiveDate' => $showEffectiveDate ? config('homepage.legal.effective_date', 'May 11, 2026') : null,
        ]);
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'in:General,Feedbacks,Grievance,Others'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $recipient = config('homepage.contact_form_to')
            ?: config('homepage.contact.email');

        $gnatMail = app(GnatMailService::class);

        try {
            if ($gnatMail->adminRecipients() === []) {
                throw new \RuntimeException('GNAT admin mail recipients are not configured.');
            }
            $gnatMail->sendWebsiteContactAdmin($data);
        } catch (\Throwable $e) {
            Log::error('Contact form email failed', [
                'exception' => $e->getMessage(),
                'recipient' => $recipient,
                'from_email' => $data['email'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'We could not send your message right now. Please try again in a moment or email us directly at '.$recipient.'.',
                ], 422);
            }

            return redirect()
                ->route('contact')
                ->withInput()
                ->withErrors([
                    'send' => 'We could not send your message right now. Please try again in a moment or email us directly at '.$recipient.'.',
                ]);
        }

        try {
            $gnatMail->sendSupportConfirmation(
                $data['email'],
                trim((string) $data['name']),
                isset($data['phone']) ? trim((string) $data['phone']) : null
            );
        } catch (\Throwable $e) {
            Log::warning('Contact form sender acknowledgment email failed', [
                'exception' => $e->getMessage(),
                'recipient' => $recipient,
                'sender_email' => $data['email'],
            ]);
        }

        Log::info('Public contact form submission emailed', [
            'recipient' => $recipient,
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'ip' => $request->ip(),
        ]);

        $successMessage = 'Thanks! Your message has been sent to our team. We have also emailed a copy to your address for your records.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $successMessage,
            ]);
        }

        return redirect()
            ->route('contact')
            ->with('success', $successMessage);
    }
}
