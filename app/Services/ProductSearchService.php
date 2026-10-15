<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ProductFilterDto;
use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;

class ProductSearchService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function ensureIndex(): void
    {
        $index = $this->indexName();

        if ($this->client->indices()->exists(['index' => $index])->asBool()) {
            return;
        }

        $this->client->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'long'],
                        'name' => [
                            'type' => 'text',
                            'analyzer' => 'russian',
                            'fields' => ['keyword' => ['type' => 'keyword']],
                        ],
                        'description' => ['type' => 'text', 'analyzer' => 'russian'],
                        'price' => ['type' => 'float'],
                        'stock' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);
    }

    public function indexProduct(Product $product, bool $refresh = true): void
    {
        $this->ensureIndex();

        $this->client->index([
            'index' => $this->indexName(),
            'id' => (string) $product->id,
            'refresh' => $refresh ? 'wait_for' : 'false',
            'body' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description ?? '',
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'created_at' => $product->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function deleteProduct(Product $product): void
    {
        if (!$this->client->indices()->exists(['index' => $this->indexName()])->asBool()) {
            return;
        }

        try {
            $this->client->delete([
                'index' => $this->indexName(),
                'id' => (string) $product->id,
                'refresh' => 'wait_for',
            ]);
        } catch (ClientResponseException $exception) {
            if ($exception->getCode() !== 404) {
                throw $exception;
            }
        }
    }

    public function reindex(): int
    {
        $this->ensureIndex();
        $count = 0;

        Product::query()->chunkById(100, function ($products) use (&$count): void {
            foreach ($products as $product) {
                $this->indexProduct($product, false);
                $count++;
            }
        });

        $this->client->indices()->refresh(['index' => $this->indexName()]);

        return $count;
    }

    /**
     * @return array{ids: array<int, int>, total: int}
     */
    public function search(ProductFilterDto $filter, int $page): array
    {
        $this->ensureIndex();
        $filters = [];
        $price = [];

        if ($filter->min_price !== null) {
            $price['gte'] = $filter->min_price;
        }

        if ($filter->max_price !== null) {
            $price['lte'] = $filter->max_price;
        }

        if ($price !== []) {
            $filters[] = ['range' => ['price' => $price]];
        }

        if ($filter->in_stock) {
            $filters[] = ['range' => ['stock' => ['gt' => 0]]];
        }

        $sort = match ($filter->sort) {
            'price_asc' => [['price' => 'asc']],
            'price_desc' => [['price' => 'desc']],
            'name_asc' => [['name.keyword' => 'asc']],
            'name_desc' => [['name.keyword' => 'desc']],
            'stock_asc' => [['stock' => 'asc']],
            'stock_desc' => [['stock' => 'desc']],
            default => [['_score' => 'desc'], ['created_at' => 'desc']],
        };
        $sort[] = ['id' => 'desc'];

        $response = $this->client->search([
            'index' => $this->indexName(),
            'body' => [
                'from' => ($page - 1) * $filter->per_page,
                'size' => $filter->per_page,
                'track_total_hits' => true,
                '_source' => false,
                'query' => [
                    'bool' => [
                        'must' => [[
                            'multi_match' => [
                                'query' => trim((string) $filter->q),
                                'fields' => ['name^3', 'description'],
                                'operator' => 'and',
                                'fuzziness' => 'AUTO',
                            ],
                        ]],
                        'filter' => $filters,
                    ],
                ],
                'sort' => $sort,
            ],
        ])->asArray();

        return [
            'ids' => array_map(
                static fn (array $hit): int => (int) $hit['_id'],
                $response['hits']['hits'],
            ),
            'total' => (int) $response['hits']['total']['value'],
        ];
    }

    private function indexName(): string
    {
        return (string) config('elasticsearch.indices.products', 'products');
    }
}
