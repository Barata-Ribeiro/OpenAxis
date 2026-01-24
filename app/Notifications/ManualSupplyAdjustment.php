<?php

namespace App\Notifications;

use App\Models\StockMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ManualSupplyAdjustment extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private StockMovement $stockMovement)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Manual supply adjustment recorded.',
            'product' => $this->stockMovement->product->name,
            'movement_type' => $this->stockMovement->movement_type,
            'quantity' => $this->stockMovement->quantity,
            'reason' => $this->stockMovement->reason,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => 'Manual supply adjustment recorded.',
            'product' => $this->stockMovement->product->name,
            'movement_type' => $this->stockMovement->movement_type,
            'quantity' => $this->stockMovement->quantity,
            'reason' => $this->stockMovement->reason,
        ]);
    }
}
