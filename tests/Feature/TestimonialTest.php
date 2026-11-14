<?php

use App\Models\Testimonial;
use App\Models\User;

test('published testimonials appear on the home page', function () {
    $visible = Testimonial::factory()->create([
        'author_name' => 'Aung Aung',
        'quote' => 'Clear communication and solid Laravel work.',
        'is_published' => true,
    ]);
    Testimonial::factory()->create([
        'author_name' => 'Hidden Person',
        'is_published' => false,
    ]);

    $this->get('/')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->has('testimonials', 1)
        ->where('testimonials.0.author_name', $visible->author_name)
    );
});

test('admin can add a testimonial', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/testimonials', [
        'author_name' => 'Su Su',
        'author_role' => 'Product manager',
        'quote' => 'The project shipped on time.',
        'sort_order' => 1,
        'is_published' => true,
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('testimonials', [
        'author_name' => 'Su Su',
        'quote' => 'The project shipped on time.',
    ]);
});

test('guests cannot add a testimonial', function () {
    $this->post('/admin/testimonials', [
        'author_name' => 'Guest',
        'quote' => 'Should not save.',
    ])->assertRedirect('/login');
});
