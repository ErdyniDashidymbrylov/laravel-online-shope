<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'smartfony' => 'Смартфоны',
            'noutbuki' => 'Ноутбуки',
            'naushniki' => 'Наушники',
            'televizory' => 'Телевизоры',
            'aksessuary' => 'Аксессуары',
        ];

        $categoryIds = [];
        foreach ($categories as $slug => $name) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );
            $categoryIds[$slug] = $category->id;
        }

        Product::query()->delete();

        $products = [
            ['name' => 'iPhone 15 128GB', 'description' => 'Смартфон Apple с OLED-дисплеем 6.1"', 'price' => 89990, 'stock' => 14, 'category' => 'smartfony'],
            ['name' => 'Samsung Galaxy S24 256GB', 'description' => 'Флагманский Android-смартфон', 'price' => 82990, 'stock' => 19, 'category' => 'smartfony'],
            ['name' => 'Xiaomi 14 12/256', 'description' => 'Смартфон с мощной камерой Leica', 'price' => 64990, 'stock' => 22, 'category' => 'smartfony'],
            ['name' => 'MacBook Air 13 M3', 'description' => 'Легкий ноутбук Apple для работы и учебы', 'price' => 139990, 'stock' => 7, 'category' => 'noutbuki'],
            ['name' => 'ASUS Vivobook 15 i5', 'description' => 'Универсальный ноутбук для офиса и дома', 'price' => 72990, 'stock' => 11, 'category' => 'noutbuki'],
            ['name' => 'Lenovo Legion 5 Ryzen 7', 'description' => 'Игровой ноутбук с RTX-графикой', 'price' => 124990, 'stock' => 5, 'category' => 'noutbuki'],
            ['name' => 'AirPods Pro 2', 'description' => 'Беспроводные наушники с активным шумоподавлением', 'price' => 24990, 'stock' => 26, 'category' => 'naushniki'],
            ['name' => 'Sony WH-1000XM5', 'description' => 'Полноразмерные наушники премиум-класса', 'price' => 32990, 'stock' => 9, 'category' => 'naushniki'],
            ['name' => 'LG OLED55 C3', 'description' => '55-дюймовый OLED-телевизор 4K', 'price' => 129990, 'stock' => 4, 'category' => 'televizory'],
            ['name' => 'Samsung QLED 65 Q70C', 'description' => '65-дюймовый QLED-телевизор 4K', 'price' => 149990, 'stock' => 3, 'category' => 'televizory'],
            ['name' => 'Baseus USB-C 65W Charger', 'description' => 'Сетевое зарядное устройство с быстрой зарядкой', 'price' => 3990, 'stock' => 35, 'category' => 'aksessuary'],
            ['name' => 'Anker PowerCore 20000', 'description' => 'Внешний аккумулятор 20000 мАч', 'price' => 5990, 'stock' => 28, 'category' => 'aksessuary'],
        ];

        foreach ($products as $product) {
            Product::query()->create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $categoryIds[$product['category']] ?? null,
                'image' => null,
            ]);
        }
    }
}
