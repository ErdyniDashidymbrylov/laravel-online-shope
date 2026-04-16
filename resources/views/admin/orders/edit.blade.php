@extends('admin.layout')

@section('title', 'Редактирование заказа #' . $order->id)
@section('page_title', 'Редактирование заказа')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="admin-card p-3">
                <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">Назад</a>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-dark btn-sm">Просмотр</a>
                </div>

                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('admin.orders._form', ['order' => $order, 'users' => $users, 'statuses' => $statuses])

                    <button type="submit" class="btn btn-primary">Обновить</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="admin-card p-3">
                <h2 class="h6 text-danger">Опасная зона</h2>
                <p class="text-muted">Удаление заказа необратимо.</p>
                <form action="{{ route('admin.orders.destroy', $order) }}"
                      method="POST"
                      onsubmit="return confirm('Удалить заказ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Удалить заказ</button>
                </form>
            </div>
        </div>
    </div>
@endsection
