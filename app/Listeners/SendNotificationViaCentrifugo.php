<?php

namespace App\Listeners;

use App\Events\NotificationSent;
use App\Services\CentrifugoService;

class SendNotificationViaCentrifugo
{
    public function __construct(
        private CentrifugoService $centrifugo,
    ) {}

    public function handle(NotificationSent $event): void
    {
        $event->broadcast($this->centrifugo);
    }
}
