@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">Корзина</h1>

        <div id="cart-content">
            @include('cart._content', ['items' => $items, 'totalQuantity' => $totalQuantity, 'totalPrice' => $totalPrice])
        </div>
    </div>
@endsection
<form method="POST" action="{{ route('orders.store') }}">
    @csrf

    <h3 class="h6 mb-2">Способ оплаты</h3>

    <div class="form-check">
        <input class="form-check-input"
               type="radio"
               name="payment_method"
               id="payment-cash"
               value="cash"
            @checked(old('payment_method', 'cash') === 'cash')>
        <label class="form-check-label" for="payment-cash">
            Наличными при получении
        </label>
    </div>

    <div class="form-check mt-1">
        <input class="form-check-input"
               type="radio"
               name="payment_method"
               id="payment-card"
               value="card"
            @checked(old('payment_method') === 'card')>
        <label class="form-check-label" for="payment-card">
            Картой при получении
        </label>
    </div>

    <button type="submit" class="btn btn-primary mt-3">
        Оформить заказ
    </button>
</form>
