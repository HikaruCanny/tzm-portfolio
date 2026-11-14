<?php

use App\Models\User;

test('admin can create a service with a chosen icon', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/services', [
        'title' => 'API design',
        'description' => 'Versioned JSON APIs for web and mobile clients.',
        'icon' => 'Server',
        'sort_order' => 0,
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('services', [
        'title' => 'API design',
        'icon' => 'Server',
    ]);
});

test('admin cannot save a service icon the site does not draw', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/services', [
        'title' => 'Unknown icon',
        'description' => 'This icon name is not in the picker.',
        'icon' => 'NotARealIcon',
        'sort_order' => 0,
    ])->assertSessionHasErrors('icon');

    $this->assertDatabaseMissing('services', [
        'title' => 'Unknown icon',
    ]);
});

test('guests cannot create a service', function () {
    $this->post('/admin/services', [
        'title' => 'Guest service',
        'description' => 'Should not save.',
        'icon' => 'Wrench',
    ])->assertRedirect('/login');
});
