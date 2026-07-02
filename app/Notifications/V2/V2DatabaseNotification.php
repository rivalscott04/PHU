<?php

namespace App\Notifications\V2;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class V2DatabaseNotification extends Notification
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    abstract protected function payload(object $notifiable): array;

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return $this->payload($notifiable);
    }
}
