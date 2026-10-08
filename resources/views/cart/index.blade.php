@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">Корзина</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="cart-content">
            @include('cart._content', ['items' => $items, 'totalQuantity' => $totalQuantity, 'totalPrice' => $totalPrice])
        </div>

        @auth
            @if(!empty($items))
                <div class="card mt-4">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Оформление заказа</h2>

                        <form method="POST" action="{{ route('orders.store') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <h3 class="h6 mb-2">Адрес доставки</h3>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="city" class="form-label">Город</label>
                                    <input
                                        type="text"
                                        id="city"
                                        name="city"
                                        class="form-control @error('city') is-invalid @enderror"
                                        value="{{ old('city', $defaultAddress?->city) }}"
                                        required
                                    >
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-5">
                                    <label for="street" class="form-label">Улица</label>
                                    <input
                                        type="text"
                                        id="street"
                                        name="street"
                                        class="form-control @error('street') is-invalid @enderror"
                                        value="{{ old('street', $defaultAddress?->street) }}"
                                        required
                                    >
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="house" class="form-label">Дом</label>
                                    <input
                                        type="text"
                                        id="house"
                                        name="house"
                                        class="form-control @error('house') is-invalid @enderror"
                                        value="{{ old('house', $defaultAddress?->house) }}"
                                        required
                                    >
                                    @error('house')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="save_address"
                                            id="save_address"
                                            value="1"
                                            @checked(old('save_address', $defaultAddress !== null))
                                        >
                                        <label class="form-check-label" for="save_address">
                                            Сохранить адрес как основной
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
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
                                               id="payment-yookassa"
                                               value="yookassa"
                                            @checked(old('payment_method') === 'yookassa')>
                                        <label class="form-check-label" for="payment-yookassa">
                                            Онлайн-оплата YooKassa
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4">
                                Оформить заказ
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @else
            @if(!empty($items))
                <div class="alert alert-info mt-4 mb-0">
                    Для оформления заказа нужно <a href="{{ route('login') }}">войти в аккаунт</a>.
                </div>
            @endif
        @endauth
    </div>
@endsection
