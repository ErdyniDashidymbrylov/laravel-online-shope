<?php

namespace App\DTO;

use App\Http\Requests\ProductFilterRequest;
use Spatie\LaravelData\Data;

class ProductFilterDto extends Data
{
    public $per_page;

    public function __construct(
        public int $perPage,
    ) {}

    public static function fromRequest(ProductFilterRequest $request): self
    {
        return new self(
            perPage: $request->validated('per_page') ?? 10
        );
    }
}
