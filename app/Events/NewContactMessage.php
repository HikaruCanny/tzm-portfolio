<?php

namespace App\Events;

use App\Models\ContactMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewContactMessage
{
    use Dispatchable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}
}
