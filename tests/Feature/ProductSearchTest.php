<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\ProductFilterDto;
use App\Models\Product;
use App\Services\ProductSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_displays_results_from_elasticsearch(): void
    {
        $iphone = $this->createProduct('iPhone 15');
        $this->createProduct('Ноутбук Lenovo');

        $this->mock(ProductSearchService::class, function (MockInterface $mock) use ($iphone): void {
            $mock->shouldReceive('search')
                ->once()
                ->withArgs(fn (ProductFilterDto $filter, int $page): bool => $filter->q === 'iPhone' && $page === 1)
                ->andReturn(['ids' => [$iphone->id], 'total' => 1]);
        });

        $this->get(route('products.index', ['q' => 'iPhone']))
            ->assertOk()
            ->assertSee('iPhone 15')
            ->assertDontSee('Ноутбук Lenovo')
            ->assertSee('Поиск товаров');
    }

    public function test_catalog_uses_database_when_elasticsearch_is_unavailable(): void
    {
        $this->createProduct('iPhone 15');
        $this->createProduct('Ноутбук Lenovo');

        $this->mock(ProductSearchService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('search')->once()->andThrow(new RuntimeException('Search unavailable'));
        });

        $this->get(route('products.index', ['q' => 'iPhone']))
            ->assertOk()
            ->assertSee('iPhone 15')
            ->assertDontSee('Ноутбук Lenovo');
    }

    public function test_product_changes_are_sent_to_search_index(): void
    {
        $this->mock(ProductSearchService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('indexProduct')->once();
            $mock->shouldReceive('deleteProduct')->once();
        });

        $product = Product::create([
            'name' => 'Смартфон',
            'price' => 10000,
            'stock' => 2,
        ]);
        $product->delete();
    }

    private function createProduct(string $name): Product
    {
        return Product::withoutEvents(fn (): Product => Product::create([
            'name' => $name,
            'price' => 10000,
            'stock' => 2,
        ]));
    }
}
