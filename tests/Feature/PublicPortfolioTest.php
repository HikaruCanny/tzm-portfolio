<?php

use App\Models\Project;

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

test('section pages open on the landing page', function (string $path, string $target) {
    $this->get($path)->assertRedirect($target);
})->with([
    ['/about', '/#about'],
    ['/experience', '/#experience'],
    ['/skills', '/#skills'],
    ['/services', '/#services'],
    ['/contact', '/#contact'],
]);

test('projects page can be rendered with filterable technology tags', function () {
    $response = $this->get('/projects');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects')
        ->has('projects')
        ->has('technologies')
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
