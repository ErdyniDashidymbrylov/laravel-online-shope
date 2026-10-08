<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Новый заказ</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937;">
<h2>Оформлен новый заказ #{{ $order->id }}</h2>

<p><strong>Пользователь:</strong> {{ $order->user?->name ?? '—' }} ({{ $order->user?->email ?? '—' }})</p>
<p><strong>Статус:</strong> {{ $order->status_label }}</p>
<p><strong>Способ оплаты:</strong> {{ $order->payment_method_label }}</p>
<p><strong>Адрес доставки:</strong> {{ $order->shipping_address ?: '—' }}</p>

<table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
    <tr>
        <th align="left">Товар</th>
        <th align="right">Количество</th>
        <th align="right">Цена за единицу</th>
        <th align="right">Сумма</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product?->name ?? 'Товар удалён' }}</td>
            <td align="right">{{ (int) $item->quantity }}</td>
            <td align="right">{{ number_format((float) $item->price, 2, '.', ' ') }}</td>
            <td align="right">{{ number_format((float) $item->subtotal, 2, '.', ' ') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p><strong>Итоговая сумма:</strong> {{ number_format((float) $order->total, 2, '.', ' ') }}</p>
</body>
</html>
