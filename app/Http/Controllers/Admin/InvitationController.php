<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function __construct(private InvitationService $invitations) {}

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Invitation::class);

        return Inertia::render('Admin/Invitations/Index', [
            'invitations' => Invitation::query()->with('creator:id,name')->latest()->paginate(15),
            'inviteCode' => $request->session()->get('invite_code'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Invitation::class);

        $validated = $request->validate([
            'role' => 'required|in:admin,superadmin',
            'expires_in_days' => 'required|integer|min:1|max:30',
        ]);

        $code = $this->invitations->issue(
            $request->user(),
            $validated['role'],
            (int) $validated['expires_in_days'],
        );

        return back()->with('invite_code', $code);
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        Gate::authorize('delete', $invitation);

        $invitation->delete();

        return back()->with('success', 'Invitation removed.');
    }
}
