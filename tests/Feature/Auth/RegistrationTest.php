<?php

use App\Models\User;
use App\Services\InvitationService;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('registration without an invite code is rejected', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors('invite_code');
    $this->assertGuest();
});

test('new users can register with a valid invite', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $code = app(InvitationService::class)->issue($superadmin, 'admin', 7);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'invite_code' => $code,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);
    $this->assertDatabaseMissing('invitations', [
        'used_at' => null,
    ]);
});
