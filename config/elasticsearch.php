<?php

declare(strict_types=1);

return [
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200'),
    ],

    'indices' => [
        'products' => env('ELASTICSEARCH_INDEX', 'products'),
    ],
];
