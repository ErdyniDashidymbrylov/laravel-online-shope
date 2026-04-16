@extends('admin.layout')

@section('title', 'Заказы')
@section('page_title', 'Управление заказами')

@section('content')
    <div class="admin-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Заказы</h2>
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">Создать заказ</a>
        </div>

        @if($orders->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Email</th>
                        <th>Статус</th>
                        <th>Сумма</th>
                        <th>Создан</th>
                        <th class="text-end">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? '—' }}</td>
                            <td>{{ $order->user->email ?? '—' }}</td>
                            <td>{{ $order->status_label }}</td>
                            <td>{{ number_format((float) $order->total, 2, '.', ' ') }}</td>
                            <td>{{ $order->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">Просмотр</a>
                                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline-primary btn-sm">Редактировать</a>

                                <form action="{{ route('admin.orders.destroy', $order) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Удалить заказ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted mb-0">Заказов пока нет.</p>
        @endif
    </div>
@endsection
