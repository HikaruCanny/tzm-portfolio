<?php

use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;

test('project admin list shows ten projects per page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Project::factory()->count(11)->create();

    $this->actingAs($admin)
        ->get('/admin/projects')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 10)
            ->where('projects.total', 11)
            ->where('projects.per_page', 10)
        );
});

test('testimonial admin list shows ten quotes per page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Testimonial::factory()->count(11)->create();

    $this->actingAs($admin)
        ->get('/admin/testimonials')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('testimonials.data', 10)
            ->where('testimonials.per_page', 10)
        );
});

test('technology admin list shows ten names per page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    foreach (range(1, 11) as $index) {
        Technology::query()->create([
            'name' => "Tool {$index}",
            'slug' => "tool-{$index}",
        ]);
    }

    $this->actingAs($admin)
        ->get('/admin/technologies')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('technologies.data', 10)
            ->where('technologies.per_page', 10)
        );
});

test('experience admin list shows ten entries per page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    foreach (range(1, 11) as $index) {
        Experience::query()->create([
            'type' => 'work',
            'organization' => "Company {$index}",
            'role' => 'Developer',
            'start_date' => '2024-01-01',
            'sort_order' => $index,
        ]);
    }

    $this->actingAs($admin)
        ->get('/admin/experiences')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('experiences.data', 10)
            ->where('experiences.per_page', 10)
        );
});

test('message inbox shows fifteen messages per page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    foreach (range(1, 16) as $index) {
        ContactMessage::query()->create([
            'name' => "Visitor {$index}",
            'email' => "visitor{$index}@example.com",
            'message' => 'Hello from the contact form.',
        ]);
    }

    $this->actingAs($admin)
        ->get('/admin/messages')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('messages.data', 15)
            ->where('messages.per_page', 15)
        );
});
