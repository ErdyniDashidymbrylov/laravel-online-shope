<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Admin\ProductCreateDto;
use App\DTO\Admin\ProductUpdateDto;
use App\DTO\ProductDto;
use App\DTO\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as EloquentPaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductService
{
    public function __construct(private readonly ProductSearchService $search)
    {
    }

    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $term = trim((string) $dto->q);

        if ($term !== '') {
            try {
                $page = EloquentPaginator::resolveCurrentPage();
                $result = $this->search->search($dto, $page);
                $productsById = Product::query()->whereKey($result['ids'])->get()->keyBy('id');
                $products = collect($result['ids'])
                    ->map(static fn (int $id): ?Product => $productsById->get($id))
                    ->filter()
                    ->values();

                return (new EloquentPaginator($products, $result['total'], $dto->per_page, $page, [
                    'path' => EloquentPaginator::resolveCurrentPath(),
                ]))->appends(request()->query());
            } catch (Throwable $exception) {
                Log::warning('Поиск Elasticsearch недоступен, используется поиск в базе данных', [
                    'exception' => $exception,
                ]);
            }
        }

        $query = Product::query();

        if ($term !== '') {
            $query->where(function ($subQuery) use ($term) {
                $subQuery
                    ->where('name', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        /**
         * 2) Фильтр по цене
         */
        if ($dto->min_price !== null) {
            $query->where('price', '>=', $dto->min_price);
        }

        if ($dto->max_price !== null) {
            $query->where('price', '<=', $dto->max_price);
        }

        /**
         * 3) Только в наличии
         */
        if ($dto->in_stock) {
            $query->where('stock', '>', 0);
        }

        /**
         * 4) Сортировка (только через whitelist)
         */
        switch ($dto->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;

            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;

            case 'new':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        /**
         * Вторичная сортировка (чтобы порядок был стабильным, если значения одинаковые)
         * Например: два товара с одинаковой ценой.
         */
        $query->orderByDesc('id');

        /**
         * 5) Пагинация + сохранение query string
         * withQueryString() — чтобы при переключении страниц фильтры НЕ сбрасывались.
         */
        return $query->paginate($dto->per_page)->withQueryString();
    }

    public function getMaxProductPrice(): int
    {
        return (int) (Product::query()->max('price') ?? 0);
    }
    public function getProductsByCategoryId(int $categoryId, ProductFilterDto $dto)
    {
        $query = Product::query()
            ->where('category_id', $categoryId)
            ->with('category');

        // дальше — тот же код поиска, фильтрации и сортировки,
        // который мы уже реализовали ранее

        return $query
            ->paginate($dto->per_page)
            ->withQueryString();
    }

    public function create(ProductDto $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image) {
            $data['image'] = $dto->image->store('products', 'public');
        }

        return Product::create($data);
    }
    public function update(Product $product, ProductDto $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $dto->image->store('products', 'public');
        }

        $product->update($data);

        return $product;
    }
    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
    public function createProduct(ProductCreateDto $dto): Product
    {
        return Product::create($dto->toArray());
    }

    public function updateProduct(Product $product, ProductUpdateDto $dto): bool
    {
        return $product->update($dto->toArray());
    }

    public function deleteProduct(Product $product): bool
    {
        return $product->delete();
    }

}
