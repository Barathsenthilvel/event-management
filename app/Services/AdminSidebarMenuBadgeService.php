<?php

namespace App\Services;

use App\Models\AdminJobApplication;
use App\Models\MemberJobRequest;
use App\Models\Menu;
use App\Models\User;

class AdminSidebarMenuBadgeService
{
    /** @var array<string, int>|null */
    private ?array $counts = null;

    /**
     * @return list<array{count: int, color: string, title: string}>
     */
    public function badgesForMenu(Menu $menu): array
    {
        if ($menu->relationLoaded('children') && $menu->children->isNotEmpty()) {
            return $this->badgesForChildren($menu->children);
        }

        return $this->badgesForMenuItem($menu->route_name, $menu->title);
    }

    /**
     * @param  list<string|null>  $childRouteNames
     * @return list<array{count: int, color: string, title: string}>
     */
    public function badgesForRouteNames(array $childRouteNames): array
    {
        $merged = [];

        foreach ($childRouteNames as $routeName) {
            $merged = $this->mergeBadges($merged, $this->badgesForMenuItem($routeName, null));
        }

        return array_values($merged);
    }

    /**
     * @return list<array{count: int, color: string, title: string}>
     */
    public function badgesForMenuItem(?string $routeName, ?string $title = null): array
    {
        $type = $this->resolveMenuType($routeName, $title);
        if (! $type) {
            return [];
        }

        $counts = $this->counts();

        return match ($type) {
            'need_job' => $this->makeBadges(
                $counts['need_job_new'],
                'amber',
                'Need Job requests (New)'
            ),
            'member_approvals' => $this->makeBadges(
                $counts['members_pending_approval'],
                'amber',
                'Members awaiting approval'
            ),
            'jobs' => $this->makeBadges(
                $counts['job_applications_pending'],
                'indigo',
                'Job applications (Pending)'
            ),
            default => [],
        };
    }

    /**
     * @param  iterable<int, Menu>  $children
     * @return list<array{count: int, color: string, title: string}>
     */
    public function badgesForChildren(iterable $children): array
    {
        $merged = [];

        foreach ($children as $child) {
            $merged = $this->mergeBadges(
                $merged,
                $this->badgesForMenuItem($child->route_name, $child->title)
            );
        }

        return array_values($merged);
    }

    private function resolveMenuType(?string $routeName, ?string $title): ?string
    {
        $route = strtolower(trim((string) $routeName));
        $label = strtolower(trim((string) $title));
        $haystack = $route.' '.$label;

        if ($route !== '' && (str_contains($route, 'need-job') || str_contains($route, 'need_job'))) {
            return 'need_job';
        }

        if (str_contains($haystack, 'need job')) {
            return 'need_job';
        }

        if ($route !== '' && str_contains($route, 'pending-approvals')) {
            return 'member_approvals';
        }

        if (preg_match('/member\s*approval/i', $label) || str_contains($label, 'pending approval')) {
            return 'member_approvals';
        }

        if ($route !== '' && str_starts_with($route, 'admin.jobs')) {
            return 'jobs';
        }

        if (preg_match('/\bjobs?\b/i', $label) && ! str_contains($label, 'need job')) {
            return 'jobs';
        }

        return null;
    }

    /**
     * @param  array<string, array{count: int, color: string, title: string}>  $merged
     * @param  list<array{count: int, color: string, title: string}>  $incoming
     * @return array<string, array{count: int, color: string, title: string}>
     */
    private function mergeBadges(array $merged, array $incoming): array
    {
        foreach ($incoming as $badge) {
            $key = $badge['color'].'|'.$badge['title'];
            if (! isset($merged[$key])) {
                $merged[$key] = $badge;
            } else {
                $merged[$key]['count'] += $badge['count'];
            }
        }

        return $merged;
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        if ($this->counts !== null) {
            return $this->counts;
        }

        $this->counts = [
            'job_applications_pending' => AdminJobApplication::query()
                ->where('application_status', 'pending')
                ->count(),
            'need_job_new' => MemberJobRequest::query()
                ->where('status', 'new')
                ->count(),
            'members_pending_approval' => User::query()
                ->where('profile_completed', true)
                ->where('is_approved', false)
                ->count(),
        ];

        return $this->counts;
    }

    /**
     * @return list<array{count: int, color: string, title: string}>
     */
    private function makeBadges(int $count, string $color, string $title): array
    {
        if ($count <= 0) {
            return [];
        }

        return [[
            'count' => $count,
            'color' => $color,
            'title' => $title,
        ]];
    }

    public function formatCount(int $count): string
    {
        return $count > 99 ? '99+' : (string) $count;
    }

    public function badgeColorClass(string $color): string
    {
        return match ($color) {
            'amber' => 'bg-amber-500',
            'rose' => 'bg-rose-500',
            default => 'bg-indigo-500',
        };
    }
}
