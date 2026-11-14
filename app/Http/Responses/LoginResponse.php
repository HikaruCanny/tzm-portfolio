<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        $user = $request->user();

        $home = $user instanceof User && $user->isAdmin()
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect()->intended($home);
    }
}
