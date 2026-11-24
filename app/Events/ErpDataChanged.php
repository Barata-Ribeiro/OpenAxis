<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

final class ErpDataChanged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(private readonly string $channel) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('erp.'.$this->channel);
    }

    /** @return array{} */
    public function broadcastWith(): array
    {
        return [];
    }
}
