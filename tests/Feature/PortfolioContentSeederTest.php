<?php

use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Testimonial;
use Database\Seeders\PortfolioContentSeeder;

test('portfolio content seeder loads phase zero dataset', function () {
    $this->seed(PortfolioContentSeeder::class);

    $profile = Profile::instance();

    expect($profile->name)->toBe(config('portfolio.name'))
        ->and($profile->goals)->not->toBeNull()
        ->and($profile->working_style)->not->toBeNull()
        ->and(Project::query()->published()->count())->toBeGreaterThanOrEqual(6)
        ->and(Post::query()->published()->count())->toBeGreaterThanOrEqual(3)
        ->and(Testimonial::query()->published()->count())->toBe(0);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('profile.name', config('portfolio.name'))
            ->has('featuredProjects', 3)
            ->has('latestPosts', 3)
        );

    expect(Profile::instance()->hasResume())->toBeTrue()
        ->and(Profile::instance()->photo_path)->not->toBeNull();
});
