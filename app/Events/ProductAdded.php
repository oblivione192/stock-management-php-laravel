<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets,  SerializesModels;

    public function __construct(
        public Product $product) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('product.added');
    }

    public function broadcastAs(): string
    {
        return 'product.added';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'sku' => $this->product->sku,
            'moved_at' => $this->product->moved_at,
            'category_id' => $this->product->category_id,
            'supplier_id' => $this->product->supplier_id,
            'unit' => $this->product->unit,
            'cost_price' => $this->product->cost_price,
            'selling_price' => $this->product->selling_price,
            'reorder_level' => $this->product->reorder_level,
            'current_stock' => $this->product->current_stock,
        ];
    }

    public function broadcastQueue(): string
    {
        return 'default';
    }
}
