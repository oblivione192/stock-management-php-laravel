<?php

namespace App\Filters;

use Illuminate\Http\Request;

class StockMovementFilter extends BaseFilter
{
    public function __construct(
        public ?string $stockMovementType = null,
        public ?string $movedAt = null,
        public ?string $productName = null,
        public ?string $productSku = null
    ) {}

    public static function createFromRequest(Request $request): self
    {
        return new self($request->query('movement_type') ?? null,
            $request->query('moved_at') ?? null,
            $request->query('product_name') ?? null,
            $request->query('product_sku') ?? null);
    }
}
