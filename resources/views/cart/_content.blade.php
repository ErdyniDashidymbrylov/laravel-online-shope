@if(empty($items))
    <div class="alert alert-info mb-0">
        Корзина пуста. <a href="{{ route('categories.index') }}">Перейти в каталог</a>
    </div>
@else
    {{-- 1. Добавляем цикл по элементам корзины --}}
    @foreach($items as $item)
        @php
            $product = $item['product']; // Достаем объект продукта из массива элемента
        @endphp

        <div class="d-flex align-items-center gap-2 mb-3">
            {{-- Кнопка МИНУС --}}
            <form method="POST" action="{{ route('cart.items.update', $product) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] <= 1)>
                    −
                </button>
            </form>

            {{-- Поле ввода количества --}}
            <form method="POST" action="{{ route('cart.items.update', $product) }}">
                @csrf
                @method('PATCH')
                <input type="number"
                       name="quantity"
                       value="{{ $item['quantity'] }}"
                       min="1"
                       max="{{ $product->stock }}"
                       class="form-control form-control-sm text-center"
                       style="width: 70px"
                       onchange="this.form.submit()"> {{-- Автоотправка при изменении --}}
            </form>

            {{-- Кнопка ПЛЮС --}}
            <form method="POST" action="{{ route('cart.items.update', $product) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] >= $product->stock)>
                    +
                </button>
            </form>

            {{-- Удаление --}}
            <form method="POST" action="{{ route('cart.items.destroy', $product) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
            </form>
        </div>
    @endforeach

    <div class="mt-4">
        <strong>Итого: {{ number_format($totalPrice, 2, '.', ' ') }} руб.</strong>
    </div>
@endif
