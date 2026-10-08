<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ оформлен</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937;">
<h2>Заказ #{{ $order->id }} успешно оформлен</h2>

<p>Здравствуйте, {{ $order->user?->name ?? 'покупатель' }}.</p>
<p>Мы получили ваш заказ. Ниже состав и параметры оплаты.</p>

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
<p><strong>Способ оплаты:</strong> {{ $order->payment_method_label }}</p>
<p><strong>Адрес доставки:</strong> {{ $order->shipping_address ?: '—' }}</p>

<p>Спасибо за заказ.<br>Команда Laravel Shop</p>
</body>
</html>
