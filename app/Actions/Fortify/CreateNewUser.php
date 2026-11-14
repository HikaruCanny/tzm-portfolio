<?php

namespace App\Actions\Fortify;

use App\Actions\RegisterInvitedAdmin;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'invite_code' => ['required', 'string', 'max:64'],
        ])->validate();

        return app(RegisterInvitedAdmin::class)([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'invite_code' => $input['invite_code'],
        ]);
    }
}
