<?php

use App\Models\Post;
use App\Models\Project;

test('robots txt allows public pages and points to sitemap', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('User-agent: *', false)
        ->assertSee('Disallow: /admin', false)
        ->assertSee(route('sitemap'), false);
});

test('sitemap xml includes static routes and published content', function () {
    $project = Project::factory()->create([
        'slug' => 'seo-sitemap-project',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $post = Post::factory()->create([
        'slug' => 'seo-sitemap-post',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(route('home'), false)
        ->assertSee(route('projects.show', $project->slug), false)
        ->assertSee(route('blog.post', $post->slug), false);
});

test('home page includes canonical description and person json ld', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('rel="canonical"', false)
        ->assertSee('name="description"', false)
        ->assertSee('property="og:title"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type":"Person"', false);
});

test('security headers are applied to html responses', function () {
    $this->get('/about')
        ->assertOk()
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});
