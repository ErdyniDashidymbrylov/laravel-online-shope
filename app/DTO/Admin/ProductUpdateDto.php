<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use App\Http\Requests\Admin\Product\UpdateRequest;

readonly class ProductUpdateDto
{
    public function __construct(
        public array $data // Храним только те поля, которые пришли в запросе
    ) {
    }

    public static function fromRequest(UpdateRequest $request): self
    {
        return new self($request->validated());
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
