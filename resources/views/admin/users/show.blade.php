@extends('admin.layout')

@section('title', 'Пользователь #' . $user->id)
@section('page_title', 'Пользователь #' . $user->id)

@section('content')
    <div class="admin-card p-3">
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Назад</a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">Редактировать</a>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6"><strong>ID:</strong> {{ $user->id }}</div>
            <div class="col-12 col-md-6"><strong>Имя:</strong> {{ $user->name }}</div>
            <div class="col-12 col-md-6"><strong>Email:</strong> {{ $user->email }}</div>
            <div class="col-12 col-md-6"><strong>Роль:</strong> {{ $user->roles->pluck('name')->join(', ') ?: '—' }}</div>
            <div class="col-12 col-md-6"><strong>Создан:</strong> {{ $user->created_at?->format('d.m.Y H:i') }}</div>
            <div class="col-12 col-md-6"><strong>Обновлён:</strong> {{ $user->updated_at?->format('d.m.Y H:i') }}</div>
        </div>
    </div>
@endsection
