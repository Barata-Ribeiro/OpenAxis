<?php

namespace App\Notifications;

use App\Models\SalesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewSalesOrder extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private SalesOrder $salesOrder)
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
            'message' => 'A new sales order has been created.',
            'sales_order_id' => $this->salesOrder->id,
            'order_number' => $this->salesOrder->order_number,
            'order_date' => $this->salesOrder->order_date,
            'total_cost' => $this->salesOrder->total_cost,
            'client' => $this->salesOrder->client->name,
            'vendor' => $this->salesOrder->vendor->name,
            'created_by' => $this->salesOrder->user->name,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => 'A new sales order has been created.',
            'sales_order_id' => $this->salesOrder->id,
            'order_number' => $this->salesOrder->order_number,
            'order_date' => $this->salesOrder->order_date,
            'total_cost' => $this->salesOrder->total_cost,
            'client' => $this->salesOrder->client->name,
            'vendor' => $this->salesOrder->vendor->name,
            'created_by' => $this->salesOrder->user->name,
        ]);
    }
}
