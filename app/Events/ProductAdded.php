<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProductAdded implements ShouldBroadcast
{
    /**
     * @var Product
     */
    public $product;

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('product.added');
    }

    public function broadcastAs(): string
    {
        return 'product.added';
    }
}
