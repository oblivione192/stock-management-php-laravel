<?php

namespace App\Events;

use App\Models\StockMovement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockOut implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StockMovement $stockMovement,
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('stockMovements');
    }

    public function broadcastAs(): string
    {
        return 'stock.out';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->stockMovement->id,
            'quantity' => $this->stockMovement->quantity,
            'reference' => $this->stockMovement->reference,
            'notes' => $this->stockMovement->notes,
            'moved_at' => $this->stockMovement->moved_at,
        ];
    }
}
