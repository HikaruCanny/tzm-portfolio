<?php

use App\Models\Project;
use App\Support\PortfolioCover;

test('home page can be rendered with dynamic portfolio props', function () {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->has('profile')
        ->has('experiences')
        ->has('categories')
        ->has('services')
        ->has('featuredProjects')
        ->has('latestPosts')
        ->has('testimonials')
    );
});

test('home shows three featured projects', function () {
    foreach (range(1, 5) as $number) {
        Project::factory()->create([
            'slug' => "featured-{$number}",
            'is_featured' => true,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    $this->get('/')->assertInertia(fn ($page) => $page->has('featuredProjects', 3));
});

test('dedicated public portfolio pages render', function (string $path, string $component) {
    $this->get($path)
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component($component));
})->with([
    ['/about', 'About'],
    ['/experience', 'Experience'],
    ['/skills', 'Skills'],
    ['/services', 'Services'],
    ['/contact', 'Contact'],
]);

test('about page includes profile story fields', function () {
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('About')
            ->has('profile', fn ($profile) => $profile
                ->has('name')
                ->has('bio')
                ->etc()
            )
        );
});

test('contact page includes faq accordion data', function () {
    $this->get('/contact')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Contact')
            ->has('profile')
            ->has('faqs', 5)
            ->has('faqs.0.question')
            ->has('faqs.0.answer')
        );
});

test('projects page can be rendered with filterable technology tags', function () {
    $response = $this->get('/projects');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects')
        ->has('projects')
        ->has('technologies')
    );
});

test('project without cover uses default project cover url', function () {
    $project = Project::factory()->make(['cover_image' => null]);

    expect($project->cover_url)->toBe(PortfolioCover::assetUrl(null, PortfolioCover::PROJECT));
});

test('projects list exposes default cover url when no upload', function () {
    Project::factory()->create([
        'slug' => 'no-cover-project',
        'cover_image' => null,
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->get('/projects')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Projects')
            ->where('projects.0.slug', 'no-cover-project')
            ->where('projects.0.cover_url', PortfolioCover::assetUrl(null, PortfolioCover::PROJECT))
        );
});

test('project case study page can be rendered for published project', function () {
    $project = Project::factory()->create([
        'title' => 'E-Commerce Platform',
        'slug' => 'e-commerce-platform',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get('/projects/e-commerce-platform');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Show')
        ->has('project')
        ->where('project.slug', 'e-commerce-platform')
    );
});

test('draft project case study returns 404', function () {
    Project::factory()->create([
        'slug' => 'secret-internal-tool',
        'status' => 'draft',
    ]);

    $response = $this->get('/projects/secret-internal-tool');
    $response->assertNotFound();
});

test('visitor can submit contact message', function () {
    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Project Inquiry',
        'message' => 'Hello TZM, I would like to discuss building a Laravel Vue project.',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'is_spam' => false,
    ]);
});

test('bot filling honeypot is marked as spam quietly', function () {
    $response = $this->post('/contact', [
        'name' => 'Spam Bot',
        'email' => 'bot@example.com',
        'subject' => 'Crypto Spam',
        'message' => 'Buy our crypto tokens now',
        '_hp' => 'I am a malicious bot',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Spam Bot',
        'is_spam' => true,
    ]);
});
