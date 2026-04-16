@extends('admin.layout')

@section('title', 'Заказ #' . $order->id)
@section('page_title', 'Заказ #' . $order->id)

@section('content')
    @php($itemsTotal = (float) $order->items->sum(fn ($item) => (float) $item->price * (int) $item->quantity))
    <div class="admin-card p-3 mb-3">
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">Назад</a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline-primary btn-sm">Редактировать</a>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6"><strong>ID:</strong> {{ $order->id }}</div>
            <div class="col-12 col-md-6">
                <strong>Пользователь:</strong>
                @if($order->user)
                    {{ $order->user->name }} ({{ $order->user->email }})
                @else
                    —
                @endif
            </div>
            <div class="col-12 col-md-6"><strong>Статус:</strong> {{ $order->status_label }}</div>
            <div class="col-12 col-md-6"><strong>Оплата:</strong> {{ $order->payment_method_label ?: '—' }}</div>
            <div class="col-12 col-md-6"><strong>Сумма заказа:</strong> {{ number_format((float) $order->total, 2, '.', ' ') }}</div>
            <div class="col-12 col-md-6"><strong>Сумма по позициям:</strong> {{ number_format($itemsTotal, 2, '.', ' ') }}</div>
            <div class="col-12"><strong>Адрес доставки:</strong> {{ $order->shipping_address ?: '—' }}</div>
            @if($order->address)
                <div class="col-12">
                    <strong>Адрес из профиля:</strong>
                    {{ $order->address->city }}, {{ $order->address->street }}, {{ $order->address->house }}
                </div>
            @endif
            <div class="col-12 col-md-6"><strong>Создан:</strong> {{ $order->created_at?->format('d.m.Y H:i') }}</div>
            <div class="col-12 col-md-6"><strong>Обновлён:</strong> {{ $order->updated_at?->format('d.m.Y H:i') }}</div>
        </div>
    </div>

    <div class="admin-card p-3">
        <h2 class="h6 mb-3">Состав заказа</h2>

        @if($order->items->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Товар</th>
                        <th class="text-end">Цена</th>
                        <th class="text-end">Кол-во</th>
                        <th class="text-end">Сумма</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                @if($item->product)
                                    {{ $item->product->name }} <span class="text-muted">#{{ $item->product->id }}</span>
                                @else
                                    <span class="text-muted">Товар удалён</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $item->price, 2, '.', ' ') }}</td>
                            <td class="text-end">{{ (int) $item->quantity }}</td>
                            <td class="text-end">{{ number_format((float) $item->subtotal, 2, '.', ' ') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Итого:</th>
                        <th class="text-end">{{ number_format($itemsTotal, 2, '.', ' ') }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Позиции заказа отсутствуют.</p>
        @endif
    </div>
@endsection
