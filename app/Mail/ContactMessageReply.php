<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\ContactReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public ContactReply $reply,
        public string $adminName,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->contactMessage->subject
            ? 'Re: '.$this->contactMessage->subject
            : 'Reply from '.config('app.name');

        return new Envelope(
            subject: $subject,
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact.reply',
            with: [
                'visitorName' => $this->contactMessage->name,
                'adminName' => $this->adminName,
                'replyBody' => $this->reply->body,
                'originalMessage' => $this->contactMessage->message,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
