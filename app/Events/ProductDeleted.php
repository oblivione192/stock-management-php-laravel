<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Product $product
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('products');
    }

    public function broadcastAs(): string
    {
        return 'product.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->product->id,
            'loss' => [
                'current_stock' => $this->product->current_stock,
                'cost_price' => $this->product->cost_price,
            ],
        ];
    }

}
