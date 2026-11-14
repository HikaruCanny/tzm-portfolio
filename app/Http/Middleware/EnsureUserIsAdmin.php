<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $role = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isDisabled()) {
            auth()->logout();

            abort(403, 'This account is disabled.');
        }

        if ($role === 'superadmin' && ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Superadmin access required.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
