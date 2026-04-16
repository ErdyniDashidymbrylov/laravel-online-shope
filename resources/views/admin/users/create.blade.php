@extends('admin.layout')

@section('title', 'Создание пользователя')
@section('page_title', 'Создание пользователя')

@section('content')
    <div class="admin-card p-3">
        <div class="mb-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Назад к списку</a>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            @include('admin.users._form', ['user' => null, 'roles' => $roles])
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
