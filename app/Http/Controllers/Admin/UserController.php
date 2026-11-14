<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', User::class);

        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role', 'disabled_at', 'created_at']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,superadmin',
        ]);

        Gate::authorize('assignRole', [$user, $validated['role']]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'Role updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $decision = Gate::inspect('delete', $user);

        if ($decision->denied()) {
            return back()->withErrors([
                'user' => $decision->message() ?: 'You cannot disable this account.',
            ]);
        }

        $user->update(['disabled_at' => now()]);

        return back()->with('success', 'Account disabled.');
    }
}
