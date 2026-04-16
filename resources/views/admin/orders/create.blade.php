@extends('admin.layout')

@section('title', 'Создание заказа')
@section('page_title', 'Создание заказа')

@section('content')
    <div class="admin-card p-3">
        <div class="mb-3">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">Назад к списку</a>
        </div>

        <form action="{{ route('admin.orders.store') }}" method="POST">
            @csrf
            @include('admin.orders._form', ['order' => null, 'users' => $users, 'statuses' => $statuses])
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
