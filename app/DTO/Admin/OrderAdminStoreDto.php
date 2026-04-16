<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use App\Http\Requests\Admin\orders\StoreOrderRequest;

readonly class OrderAdminStoreDto
{
    public function __construct(
        public int $userId,
        public string $status,
        public float $total,
    ) {
    }

    public static function fromRequest(StoreOrderRequest $request): self
    {
        return new self(
            userId: (int) $request->validated('user_id'),
            status: (string) $request->validated('status'),
            total: (float) $request->validated('total'),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'status' => $this->status,
            'total' => $this->total,
        ];
    }
}
