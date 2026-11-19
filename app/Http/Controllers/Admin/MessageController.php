<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReplyToContactMessageRequest;
use App\Mail\ContactMessageReply;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        $filter = $request->string('filter', 'all')->toString();

        if (! in_array($filter, ['all', 'unread', 'spam', 'handled'], true)) {
            $filter = 'all';
        }

        $query = ContactMessage::query()
            ->with(['replies' => fn ($q) => $q->latest()->with('user:id,name')])
            ->latest();

        match ($filter) {
            'unread' => $query->unread(),
            'spam' => $query->spam(),
            'handled' => $query->handled(),
            default => $query,
        };

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $query->paginate(15)->withQueryString(),
            'filter' => $filter,
        ]);
    }

    public function markAsRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => now()]);

        return back()->with('success', 'Message marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        ContactMessage::query()->unread()->update(['read_at' => now()]);

        return back()->with('success', 'All unread messages marked as read.');
    }

    public function markAsHandled(ContactMessage $message): RedirectResponse
    {
        $message->update([
            'handled_at' => now(),
            'read_at' => $message->read_at ?? now(),
        ]);

        return back()->with('success', 'Message marked as handled.');
    }

    public function reply(ReplyToContactMessageRequest $request, ContactMessage $message): RedirectResponse
    {
        if ($message->is_spam) {
            return back()->withErrors(['body' => 'Cannot reply to spam messages.']);
        }

        $reply = $message->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
            'sent_at' => now(),
        ]);

        Mail::to($message->email)->send(new ContactMessageReply($message, $reply, $request->user()->name));

        $message->update([
            'handled_at' => $message->handled_at ?? now(),
            'read_at' => $message->read_at ?? now(),
        ]);

        return back()->with('success', 'Reply sent to the visitor.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Message deleted.');
    }
}
