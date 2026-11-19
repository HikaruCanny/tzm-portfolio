<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkContactMessageRequest;
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

        if (! in_array($filter, ['all', 'unread', 'spam', 'handled', 'archived'], true)) {
            $filter = 'all';
        }

        $query = ContactMessage::query()
            ->with(['replies' => fn ($q) => $q->latest()->with('user:id,name')])
            ->latest();

        match ($filter) {
            'unread' => $query->unread()->active(),
            'spam' => $query->spam(),
            'handled' => $query->handled()->active(),
            'archived' => $query->archived(),
            default => $query->active(),
        };

        $search = $request->string('q')->trim()->toString();
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%');
            });
        }

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $query->paginate(10)->withQueryString(),
            'filter' => $filter,
            'search' => $search,
        ]);
    }

    public function markAsRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => now()]);

        return back()->with('success', 'Message marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        ContactMessage::query()->unread()->active()->update(['read_at' => now()]);

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

    public function archive(ContactMessage $message): RedirectResponse
    {
        $message->update([
            'archived_at' => now(),
            'read_at' => $message->read_at ?? now(),
        ]);

        return back()->with('success', 'Message archived.');
    }

    public function unarchive(ContactMessage $message): RedirectResponse
    {
        $message->update(['archived_at' => null]);

        return back()->with('success', 'Message restored from archive.');
    }

    public function bulk(BulkContactMessageRequest $request): RedirectResponse
    {
        $action = $request->validated('action');
        $ids = $request->validated('ids');

        $query = ContactMessage::query()->whereIn('id', $ids);
        $count = $query->count();

        match ($action) {
            'read' => $query->update(['read_at' => now()]),
            'handled' => $query->notSpam()->update([
                'handled_at' => now(),
                'read_at' => now(),
            ]),
            'archive' => $query->update([
                'archived_at' => now(),
                'read_at' => now(),
            ]),
            'unarchive' => $query->update(['archived_at' => null]),
            'delete' => $query->delete(),
        };

        $label = match ($action) {
            'read' => 'marked as read',
            'handled' => 'marked as handled',
            'archive' => 'archived',
            'unarchive' => 'restored',
            'delete' => 'deleted',
        };

        return back()->with('success', "{$count} message(s) {$label}.");
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
