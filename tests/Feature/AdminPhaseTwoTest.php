<?php

use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admins can add a technology and the action is logged', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post('/admin/technologies', ['name' => 'Laravel'])
        ->assertRedirect();

    $this->assertDatabaseHas('technologies', ['name' => 'Laravel']);
    expect(ActivityLog::query()->where('action', 'created')->exists())->toBeTrue();
});

test('a superadmin can open the portfolio admin without a second password prompt', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)
        ->get('/admin')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Dashboard'));
});

test('admins cannot open superadmin invitation management', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/invitations')
        ->assertForbidden();
});

test('superadmin can issue an invite and that code registers one admin', function () {
    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)
        ->post('/admin/invitations', [
            'role' => 'admin',
            'expires_in_days' => 7,
        ])
        ->assertRedirect()
        ->assertSessionHas('invite_code');

    $code = session('invite_code');

    auth()->logout();

    $this->post('/register', [
        'name' => 'Invited Admin',
        'email' => 'invited@example.com',
        'invite_code' => $code,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('admin.dashboard', absolute: false));

    $this->post('/logout');

    $this->post('/register', [
        'name' => 'Second Try',
        'email' => 'second@example.com',
        'invite_code' => $code,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('invite_code');
});

test('the last superadmin cannot be demoted or disabled', function () {
    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)
        ->patch('/admin/users/'.$superadmin->id, ['role' => 'admin'])
        ->assertForbidden();

    $this->actingAs($superadmin)
        ->delete('/admin/users/'.$superadmin->id)
        ->assertSessionHasErrors('user');

    expect($superadmin->fresh()->role)->toBe('superadmin');
    expect($superadmin->fresh()->disabled_at)->toBeNull();
});

test('a superadmin can disable another admin', function () {
    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($superadmin)
        ->delete('/admin/users/'.$admin->id)
        ->assertRedirect();

    expect($admin->fresh()->disabled_at)->not->toBeNull();

    $this->post('/logout');

    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('project cover images are stored as webp', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/projects', [
        'title' => 'Image Pipeline',
        'summary' => 'Uploads a cover.',
        'status' => 'draft',
        'is_featured' => false,
        'cover_image' => UploadedFile::fake()->image('cover.jpg', 1200, 800),
    ])->assertRedirect('/admin/projects');

    $project = Project::query()->where('title', 'Image Pipeline')->first();

    expect($project)->not->toBeNull();
    expect($project->cover_image)->toEndWith('.webp');
    Storage::disk('public')->assertExists($project->cover_image);
});

test('an expired invite cannot be used', function () {
    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);
    $code = app(InvitationService::class)->issue($superadmin, 'admin', 7);

    $invitation = Invitation::query()->first();
    $invitation->forceFill(['expires_at' => now()->subMinute()])->save();

    $this->post('/register', [
        'name' => 'Late User',
        'email' => 'late@example.com',
        'invite_code' => $code,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('invite_code');

    $this->assertGuest();
});
