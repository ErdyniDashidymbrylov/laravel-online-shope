<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SessionCartService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;


class CartController extends Controller
{
    public function __construct(
        private SessionCartService $sessionCartService,
    ){}

    public function index(): Factory|View
    {
        return view('cart.index', [
            'items' => $this->sessionCartService->getItems(),
            'totalQuantity' => $this->sessionCartService->getTotalQuantity(),
            'totalPrice' => $this->sessionCartService->getTotalPrice(),
        ]);
    }

    public function store(Product $product, Request $request)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->sessionCartService->add($product, (int) ($data['quantity'] ?? 1));

        return $this->respond($request);
    }

    public function update(Product $product, Request $request)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->sessionCartService->setQuantity($product, (int) $data['quantity']);

        return $this->respond($request);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->sessionCartService->remove($product);
        return $this->respond($request);
    }

    public function clear(Request $request)
    {
        $this->sessionCartService->clear();
        return $this->respond($request);
    }


    private function respond(Request $request): JsonResponse|RedirectResponse
    {
        $totalQuantity = $this->sessionCartService->getTotalQuantity();

        if ($request->expectsJson()) {
            return response()->json([
                'cartCount' => $totalQuantity,
                'html' => view('cart._content', [
                    'items'         => $this->sessionCartService->getItems(),
                    'totalQuantity' => $totalQuantity,
                    'totalPrice'    => $this->sessionCartService->getTotalPrice(),
                ])->render(),
            ]);
        }

        return redirect()
            ->back()
            ->with('cartCount', $totalQuantity);
    }
}

