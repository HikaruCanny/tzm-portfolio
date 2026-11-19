<?php

namespace App\Http\Middleware;

use App\Models\ContactMessage;
use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'admin' => fn () => $this->adminShare($request),
            'portfolio' => fn () => $this->portfolioShare(),
        ];
    }

    /**
     * @return array{has_resume: bool, resume_url: string|null}
     */
    private function portfolioShare(): array
    {
        $profile = Profile::instance();

        return [
            'has_resume' => $profile->hasResume(),
            'resume_url' => $profile->hasResume() ? route('resume.download') : null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function adminShare(Request $request): ?array
    {
        $user = $request->user();

        if (! $user?->isAdmin()) {
            return null;
        }

        return [
            'unread_notifications_count' => $user->unreadNotifications()->count(),
            'unread_messages_count' => ContactMessage::query()->unread()->count(),
            'recent_notifications' => $user->notifications()
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn ($notification) => [
                    'id' => $notification->id,
                    'read_at' => $notification->read_at,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at,
                ]),
        ];
    }
}
