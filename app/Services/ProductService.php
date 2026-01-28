<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const  PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /**
     * Возвращает список товаров с контролируемой пагинацией.
     */
    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $query = Product::query();

        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true)
            ? $dto->per_page: 10;

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }
    public function getProductPageData(Product $product): Product
    {
        // В будущем тут могут быть:
        // - проверка статуса (active/inactive)
        // - подгрузка связей
        // - вычисление скидок
        // - подготовка DTO / ViewModel

        return $product;
    }
}

