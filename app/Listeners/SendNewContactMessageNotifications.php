<?php

namespace App\Listeners;

use App\Events\NewContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;

class SendNewContactMessageNotifications
{
    public function handle(NewContactMessage $event): void
    {
        if ($event->contactMessage->is_spam) {
            return;
        }

        User::query()
            ->activeAdmins()
            ->each(fn (User $admin) => $admin->notify(new NewContactMessageNotification($event->contactMessage)));
    }
}
