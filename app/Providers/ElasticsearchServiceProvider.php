<?php

declare(strict_types=1);

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            Client::class,
            function (): Client {
                return ClientBuilder::create()
                    ->setHosts(config('elasticsearch.hosts'))
                    ->build();
            },
        );
    }
}
