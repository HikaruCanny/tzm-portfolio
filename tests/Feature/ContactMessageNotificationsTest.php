<?php

use App\Mail\ContactMessageReply;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('valid contact submission notifies active admins', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['role' => 'admin', 'disabled_at' => now()]);

    $this->post('/contact', [
        'name' => 'Alex Visitor',
        'email' => 'alex@example.com',
        'subject' => 'Collaboration',
        'message' => 'Hello, I would like to work with you.',
    ])->assertRedirect();

    $message = ContactMessage::query()->where('email', 'alex@example.com')->first();

    expect($message)->not->toBeNull()
        ->and($message->is_spam)->toBeFalse();

    Notification::assertSentTo($admin, NewContactMessageNotification::class);
    Notification::assertCount(1);
});

test('honeypot spam does not notify admins', function () {
    Notification::fake();

    User::factory()->create(['role' => 'admin']);

    $this->post('/contact', [
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'message' => 'Spam body',
        '_hp' => 'filled',
    ])->assertRedirect();

    Notification::assertNothingSent();
});

test('admin can filter inbox views', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    ContactMessage::query()->create([
        'name' => 'Unread',
        'email' => 'u@example.com',
        'message' => 'Needs attention',
        'is_spam' => false,
    ]);

    ContactMessage::query()->create([
        'name' => 'Spam',
        'email' => 's@example.com',
        'message' => 'Bad',
        'is_spam' => true,
    ]);

    ContactMessage::query()->create([
        'name' => 'Done',
        'email' => 'd@example.com',
        'message' => 'Thanks',
        'is_spam' => false,
        'handled_at' => now(),
        'read_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/messages?filter=unread')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('filter', 'unread')
            ->has('messages.data', 1)
            ->where('messages.data.0.name', 'Unread')
        );

    $this->actingAs($admin)
        ->get('/admin/messages?filter=spam')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('messages.data', 1)->where('messages.data.0.name', 'Spam'));

    $this->actingAs($admin)
        ->get('/admin/messages?filter=handled')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('messages.data', 1)->where('messages.data.0.name', 'Done'));
});

test('admin can mark message handled and reply by email', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin', 'name' => 'TZM']);

    $message = ContactMessage::query()->create([
        'name' => 'Jordan',
        'email' => 'jordan@example.com',
        'subject' => 'Project idea',
        'message' => 'Can we talk?',
        'is_spam' => false,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.messages.handled', $message))
        ->assertRedirect();

    $message->refresh();
    expect($message->handled_at)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('admin.messages.reply', $message), [
            'body' => 'Sure — happy to chat next week.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSent(ContactMessageReply::class, function (ContactMessageReply $mail) use ($message) {
        return $mail->hasTo($message->email);
    });

    expect($message->fresh()->replies)->toHaveCount(1);
});

test('admin inbox shares unread counts for layout', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    ContactMessage::query()->create([
        'name' => 'One',
        'email' => '1@example.com',
        'message' => 'Hi',
        'is_spam' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.messages.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('admin.unread_messages_count', 1)
        );
});
