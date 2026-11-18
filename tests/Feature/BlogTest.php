<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

test('blog index lists only published posts', function () {
    $published = Post::factory()->create([
        'title' => 'Published Guide',
        'slug' => 'published-guide',
        'status' => 'published',
        'published_at' => now(),
    ]);
    Post::factory()->draft()->create([
        'title' => 'Secret Draft',
        'slug' => 'secret-draft',
    ]);

    $this->get('/blog')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Blog')
            ->has('posts.data', 1)
            ->where('posts.data.0.slug', $published->slug)
            ->has('tags')
        );
});

test('blog post page renders published markdown and hides drafts', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $post = Post::factory()->create([
        'title' => 'Inertia Notes',
        'slug' => 'inertia-notes',
        'body_markdown' => "## Setup\n\nUse `php artisan serve`.",
        'status' => 'published',
        'published_at' => now(),
    ]);
    $post->tags()->attach($tag);

    Post::factory()->draft()->create(['slug' => 'hidden-draft']);

    $this->get('/blog/inertia-notes')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('BlogPost')
            ->where('post.slug', 'inertia-notes')
            ->where('post.title', 'Inertia Notes')
            ->has('post.html')
            ->has('post.toc', 1)
            ->has('relatedPosts')
        );

    $this->get('/blog/hidden-draft')->assertNotFound();
});

test('blog tag filter and search work together', function () {
    $laravel = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $vue = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue']);

    $match = Post::factory()->create([
        'title' => 'Laravel Queues',
        'slug' => 'laravel-queues',
        'excerpt' => 'Background jobs deep dive',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $match->tags()->attach($laravel);

    $other = Post::factory()->create([
        'title' => 'Vue Composables',
        'slug' => 'vue-composables',
        'excerpt' => 'Reusable state patterns',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $other->tags()->attach($vue);

    $this->get('/blog/tag/laravel?search=Queues')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Blog')
            ->has('posts.data', 1)
            ->where('posts.data.0.slug', 'laravel-queues')
            ->where('filters.tag.slug', 'laravel')
        );
});

test('rss feed includes published posts only', function () {
    Post::factory()->create([
        'title' => 'Feed Item',
        'slug' => 'feed-item',
        'status' => 'published',
        'published_at' => now(),
    ]);
    Post::factory()->draft()->create(['title' => 'Draft Feed']);

    $this->get('/feed.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->assertSee('Feed Item')
        ->assertDontSee('Draft Feed');
});

test('admins can create tags and publish posts', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post('/admin/tags', ['name' => 'Testing'])
        ->assertRedirect();

    $tag = Tag::query()->where('name', 'Testing')->firstOrFail();

    $this->actingAs($admin)
        ->post('/admin/posts', [
            'title' => 'First Real Post',
            'excerpt' => 'Short summary for the card.',
            'body' => "## Hello\n\nBody content here.",
            'status' => 'published',
            'tag_ids' => [$tag->id],
        ])
        ->assertRedirect(route('admin.posts.index'));

    $post = Post::query()->where('title', 'First Real Post')->firstOrFail();

    expect($post->status)->toBe('published');
    expect($post->published_at)->not->toBeNull();
    expect($post->reading_time)->toBeGreaterThan(0);
    expect($post->tags()->pluck('tags.id')->all())->toContain($tag->id);

    $this->actingAs($admin)
        ->get('/admin/posts')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Posts/Index')->has('posts.data', 1));

    $this->actingAs($admin)
        ->get('/admin/tags')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Tags/Index'));
});

test('guests cannot manage posts', function () {
    $this->get('/admin/posts')->assertRedirect('/login');
    $this->post('/admin/posts', [])->assertRedirect('/login');
});

test('markdown preview endpoint returns sanitized html for admins', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->postJson('/admin/posts/preview', [
            'body' => "## Title\n\n<script>alert(1)</script>\n\nHello",
        ])
        ->assertOk()
        ->assertJsonPath('html', fn (string $html) => str_contains($html, 'Title')
            && ! str_contains($html, '<script>'));
});

test('opening a published post increments views once per session', function () {
    $post = Post::factory()->create([
        'slug' => 'viewed-once',
        'status' => 'published',
        'published_at' => now(),
        'views_count' => 0,
    ]);

    $this->get('/blog/viewed-once')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('BlogPost')
            ->where('post.views_count', 1)
        );

    expect($post->fresh()->views_count)->toBe(1);

    $this->get('/blog/viewed-once')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('post.views_count', 1)
        );

    expect($post->fresh()->views_count)->toBe(1);
});

test('blog index includes cover image and views count', function () {
    Post::factory()->create([
        'slug' => 'with-cover',
        'cover_image' => 'posts/covers/demo.webp',
        'status' => 'published',
        'published_at' => now(),
        'views_count' => 12,
    ]);

    $this->get('/blog')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Blog')
            ->where('posts.data.0.slug', 'with-cover')
            ->where('posts.data.0.cover_image', 'posts/covers/demo.webp')
            ->where('posts.data.0.views_count', 12)
        );
});

test('draft posts do not receive view counts', function () {
    $draft = Post::factory()->draft()->create([
        'slug' => 'never-viewed-draft',
        'views_count' => 0,
    ]);

    $this->get('/blog/never-viewed-draft')->assertNotFound();

    expect($draft->fresh()->views_count)->toBe(0);
});
