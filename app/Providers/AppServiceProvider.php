<?php

namespace App\Providers;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\RecordsContentActivity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::define('manage-portfolio', function (User $user): bool {
            return $user->isAdmin() && ! $user->isDisabled();
        });

        Gate::define('manage-site', function (User $user): bool {
            return $user->isSuperAdmin() && ! $user->isDisabled();
        });

        $observer = RecordsContentActivity::class;

        foreach ([
            Post::class,
            Tag::class,
            Project::class,
            ProjectImage::class,
            Experience::class,
            Skill::class,
            SkillCategory::class,
            Service::class,
            Technology::class,
            Testimonial::class,
            Profile::class,
            User::class,
        ] as $model) {
            $model::observe($observer);
        }
    }
}
