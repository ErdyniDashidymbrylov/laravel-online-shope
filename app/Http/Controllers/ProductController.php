<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(ProductFilterRequest $request): View
    {
        $dto = ProductFilterDto::fromRequest($request);

        $products = $this->productService->getProducts($dto);
        $maxPrice = $this->productService->getMaxProductPrice();

        return view('products.index', compact('products', 'dto', 'maxPrice'));
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }
}
