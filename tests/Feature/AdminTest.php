<?php

use App\Models\User;

test('guests are redirected from admin dashboard to login', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/login');
});

test('authenticated admin can access admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin');
    $response->assertOk();
});

test('admin can create a new portfolio project', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/admin/projects', [
        'title' => 'Automated CI/CD Pipeline',
        'summary' => 'Zero-downtime deployment pipeline built with Docker and GitHub Actions.',
        'status' => 'published',
        'is_featured' => true,
    ]);

    $response->assertRedirect('/admin/projects');
    $this->assertDatabaseHas('projects', [
        'title' => 'Automated CI/CD Pipeline',
        'status' => 'published',
    ]);
});

test('non-superadmin cannot access superadmin profile editor', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/profile');
    $response->assertForbidden();
});

test('superadmin can access profile editor', function () {
    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);

    $response = $this->actingAs($superadmin)->get('/admin/profile');
    $response->assertOk();
});
