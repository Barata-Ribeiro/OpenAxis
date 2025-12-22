<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewPurchaseOrder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private PurchaseOrder $purchaseOrder)
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
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'A new purchase order has been created.',
            'purchase_order_id' => $this->purchaseOrder->id,
            'order_number' => $this->purchaseOrder->order_number,
            'order_date' => $this->purchaseOrder->order_date,
            'total_cost' => $this->purchaseOrder->total_cost,
            'supplier' => $this->purchaseOrder->supplier->name,
            'created_by' => $this->purchaseOrder->user->name,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => 'A new purchase order has been created.',
            'purchase_order_id' => $this->purchaseOrder->id,
            'order_number' => $this->purchaseOrder->order_number,
            'order_date' => $this->purchaseOrder->order_date,
            'total_cost' => $this->purchaseOrder->total_cost,
            'supplier' => $this->purchaseOrder->supplier->name,
            'created_by' => $this->purchaseOrder->user->name,
        ]);
    }
}
