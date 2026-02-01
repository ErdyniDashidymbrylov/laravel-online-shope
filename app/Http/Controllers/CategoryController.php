<?php

namespace App\Http\Controllers;

use App\DTO\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Models\Category;


class CategoryController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index()
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }
    public function show(Category $category, ProductFilterRequest $request)
    {
        $dto = ProductFilterDto::fromRequest($request);

        $products = $this->productService
            ->getProductsByCategoryId($category->id, $dto);

        return view('products.index', [
            'products' => $products,
            'category' => $category,
            'dto'      => $dto,
        ]);
    }

    /**
     * @return mixed
     */
    public function getProductService()
    {
        return $this->productService;
    }
}

