<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Services\ProductSearchService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductSearchObserver
{
    public function __construct(private readonly ProductSearchService $search)
    {
    }

    public function saved(Product $product): void
    {
        try {
            $this->search->indexProduct($product);
        } catch (Throwable $exception) {
            Log::warning('Не удалось обновить товар в Elasticsearch', [
                'product_id' => $product->id,
                'exception' => $exception,
            ]);
        }
    }

    public function deleted(Product $product): void
    {
        try {
            $this->search->deleteProduct($product);
        } catch (Throwable $exception) {
            Log::warning('Не удалось удалить товар из Elasticsearch', [
                'product_id' => $product->id,
                'exception' => $exception,
            ]);
        }
    }
}
