<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isActiveSuperAdmin($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isActiveSuperAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isActiveSuperAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isActiveSuperAdmin($user);
    }

    public function assignRole(User $user, User $model, string $role): Response
    {
        if (! $this->isActiveSuperAdmin($user)) {
            return Response::deny();
        }

        if (! in_array($role, ['admin', 'superadmin'], true)) {
            return Response::deny('That role is not available.');
        }

        if ($this->removesLastActiveSuperAdmin($model, $role === 'superadmin')) {
            return Response::deny('The last superadmin cannot be demoted.');
        }

        return Response::allow();
    }

    public function delete(User $user, User $model): Response
    {
        if (! $this->isActiveSuperAdmin($user)) {
            return Response::deny();
        }

        if ($user->is($model)) {
            return Response::deny('You cannot disable your own account.');
        }

        if ($this->removesLastActiveSuperAdmin($model, false)) {
            return Response::deny('The last superadmin cannot be disabled.');
        }

        return Response::allow();
    }

    private function isActiveSuperAdmin(User $user): bool
    {
        return $user->isSuperAdmin() && ! $user->isDisabled();
    }

    private function removesLastActiveSuperAdmin(User $model, bool $staysActiveSuperAdmin): bool
    {
        if (! $model->isSuperAdmin() || $model->isDisabled() || $staysActiveSuperAdmin) {
            return false;
        }

        return User::query()
            ->where('role', 'superadmin')
            ->whereNull('disabled_at')
            ->count() <= 1;
    }
}
