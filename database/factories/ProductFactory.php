<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ru_RU');

        $electronics = [
            'Смартфон',
            'Ноутбук',
            'Планшет',
            'Смарт-часы',
            'Наушники',
            'Телевизор',
            'Монитор',
            'Игровая приставка',
            'Клавиатура',
            'Мышь',
            'Роутер',
            'Колонка Bluetooth',
        ];

        $brands = [
            'Samsung',
            'Apple',
            'Xiaomi',
            'Sony',
            'LG',
            'Huawei',
            'Lenovo',
            'Asus',
            'HP',
            'Dell',
        ];

        $name = $faker->randomElement($electronics) . ' ' . $faker->randomElement($brands);

        return [
            'name' => $name,
            'description' => $faker->realText(120),
            'price' => $faker->numberBetween(5000, 150000),
            'stock' => $faker->numberBetween(0, 50),
        ];
    }
}
