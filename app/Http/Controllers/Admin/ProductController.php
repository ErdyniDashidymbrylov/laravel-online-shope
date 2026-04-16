<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\Admin\ProductCreateDto;
use App\DTO\Admin\ProductUpdateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->with('category')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', '%' . $q . '%')
                        ->orWhere('description', 'like', '%' . $q . '%')
                        ->orWhere('id', $q);
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $dto = ProductCreateDto::fromRequest($request);
        $this->productService->createProduct($dto);

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар создан');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateRequest $request, Product $product): RedirectResponse
    {
        $dto = ProductUpdateDto::fromRequest($request);
        $this->productService->updateProduct($product, $dto);

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар обновлен');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар удален');
    }
}
