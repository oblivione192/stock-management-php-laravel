<?php

namespace App\Filters;

use Illuminate\Http\Request;

class CategoryFilter extends BaseFilter
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
    ) {}

    public static function createFromRequest(Request $request): self
    {
        return new self(
            $request->query('name') ?? null,
            $request->query('description') ?? null
        );
    }
}
