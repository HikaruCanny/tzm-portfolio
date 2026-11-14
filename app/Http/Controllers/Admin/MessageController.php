<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(): Response
    {
        $messages = ContactMessage::latest()->paginate(15);

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages,
        ]);
    }

    public function markAsRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => now()]);

        return back()->with('success', 'Message marked as read.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Message deleted.');
    }
}
