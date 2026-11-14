<?php

namespace App\Actions;

use App\Models\User;
use App\Services\InvitationService;

class RegisterInvitedAdmin
{
    public function __construct(private InvitationService $invitations) {}

    /**
     * @param  array{name: string, email: string, password: string, invite_code: string}  $input
     */
    public function __invoke(array $input): User
    {
        $invitation = $this->invitations->findValid($input['invite_code']);

        $user = User::query()->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => $invitation->role,
        ]);

        $invitation->forceFill(['used_at' => now()])->save();

        return $user;
    }
}
