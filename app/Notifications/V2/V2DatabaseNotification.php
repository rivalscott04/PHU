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
        $channels = ['database'];

        // Only broadcast when Reverb is actually configured; empty key would
        // still attempt a remote publish and can hang the HTTP request.
        if (
            config('broadcasting.default') === 'reverb'
            && filled(config('broadcasting.connections.reverb.key'))
        ) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /** @return array<string, mixed> */
    abstract protected function payload(object $notifiable): array;

    /** @param  array<string, mixed>|int|string  $parameters */
    protected function actionUrl(string $name, mixed $parameters = []): string
    {
        return route($name, $parameters, absolute: false);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return $this->payload($notifiable);
    }
}
