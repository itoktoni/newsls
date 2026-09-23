<?php

namespace App\Events;

use App\Models\Notification as NotificationModel;
use App\Services\CentrifugoService;
use Illuminate\Foundation\Events\Dispatchable;

class NotificationSent
{
    use Dispatchable;

    public function __construct(
        public int $userId,
        public NotificationModel $notification,
    ) {}

    public function broadcast(CentrifugoService $centrifugo): void
    {
        $channel = 'notifications#'.$this->userId;

        $centrifugo->publish($channel, [
            'id' => $this->notification->id,
            'icon' => $this->notification->icon,
            'iconColor' => $this->notification->icon_color,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'url' => $this->notification->url,
            'type' => $this->notification->type,
            'read' => false,
            'meta' => $this->notification->meta,
            'time' => 'Baru saja',
            'created_at' => $this->notification->created_at?->toIso8601String(),
        ]);
    }
}
