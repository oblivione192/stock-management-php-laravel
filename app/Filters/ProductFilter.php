<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ProductFilter extends BaseFilter
{
    public function __construct(
        public ?string $name = null,
        public ?string $sku = null,
        public ?string $productId = null,
        public ?string $categoryId = null,
        public ?string $supplierId = null,
        public ?string $movedAt = null,
        public ?string $movementType = null,
        public ?string $createdBefore = null,
        public ?string $createdAfter = null,
    ) {}

    public static function createFromRequest(Request $request): self
    {
        return new self(
            name: $request->query('name') ?: null,
            sku: $request->query('sku') ?: null,
            productId: $request->query('product_id'),
            categoryId: $request->query('category_id'),
            supplierId: $request->query('supplier_id'),
            movedAt: $request->query('moved_at')
                ? $request->query('moved_at')
                : null,
            movementType: $request->query('movement_type'), // or resolve properly if this is meant to be an object
            createdBefore: $request->query('created_before')
                ? $request->query('created_before')
                : null,
            createdAfter: $request->query('created_after')
                ? $request->query('created_after')
                : null,
        );
    }
}
