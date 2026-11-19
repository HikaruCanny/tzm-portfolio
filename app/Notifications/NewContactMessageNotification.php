<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('New contact message from '.$this->contactMessage->name)
            ->greeting('New portfolio inquiry')
            ->line('Someone submitted your public contact form.')
            ->line('**From:** '.$this->contactMessage->name.' ('.$this->contactMessage->email.')');

        if ($this->contactMessage->subject) {
            $mail->line('**Subject:** '.$this->contactMessage->subject);
        }

        return $mail
            ->line(Str::limit($this->contactMessage->message, 240))
            ->action('Open admin inbox', route('admin.messages.index', ['filter' => 'unread']))
            ->line('You can reply directly from the admin Messages screen.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contact_message_id' => $this->contactMessage->id,
            'name' => $this->contactMessage->name,
            'email' => $this->contactMessage->email,
            'subject' => $this->contactMessage->subject,
            'excerpt' => Str::limit($this->contactMessage->message, 120),
            'url' => route('admin.messages.index', ['filter' => 'unread']),
        ];
    }
}
