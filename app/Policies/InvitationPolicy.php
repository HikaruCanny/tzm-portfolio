<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() && ! $user->isDisabled();
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->viewAny($user);
    }
}
