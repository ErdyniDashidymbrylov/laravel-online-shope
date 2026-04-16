<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use App\Http\Requests\Admin\Product\StoreRequest;

readonly class ProductCreateDto
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $categoryId,
    ) {
    }

    public static function fromRequest(StoreRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            price: (float) $request->validated('price'),
            categoryId: (int) $request->validated('category_id'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->categoryId,
        ];
    }
}
