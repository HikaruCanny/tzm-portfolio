<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvitationService
{
    public function issue(User $creator, string $role, int $expiresInDays): string
    {
        $plainCode = Str::password(32, symbols: false);

        Invitation::query()->create([
            'code_hash' => $this->hash($plainCode),
            'role' => $role,
            'created_by' => $creator->id,
            'expires_at' => now()->addDays($expiresInDays),
        ]);

        return $plainCode;
    }

    public function findValid(string $plainCode): Invitation
    {
        $invitation = Invitation::query()
            ->where('code_hash', $this->hash($plainCode))
            ->first();

        if ($invitation === null || ! $invitation->isValid()) {
            throw ValidationException::withMessages([
                'invite_code' => 'This invite code is invalid or has expired.',
            ]);
        }

        return $invitation;
    }

    public function hash(string $plainCode): string
    {
        return hash('sha256', $plainCode);
    }
}
