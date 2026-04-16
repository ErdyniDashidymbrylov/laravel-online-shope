@extends('admin.layout')

@section('title', 'Редактирование пользователя #' . $user->id)
@section('page_title', 'Редактирование пользователя')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="admin-card p-3">
                <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Назад</a>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-dark btn-sm">Просмотр</a>
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('admin.users._form', ['user' => $user, 'roles' => $roles])

                    <button type="submit" class="btn btn-primary">Обновить</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="admin-card p-3 mb-3">
                <h2 class="h6">Смена пароля</h2>

                <form action="{{ route('admin.users.password', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-outline-primary">Обновить пароль</button>
                </form>
            </div>

            <div class="admin-card p-3">
                <h2 class="h6 text-danger">Опасная зона</h2>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Удалить пользователя?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Удалить пользователя</button>
                </form>
            </div>
        </div>
    </div>
@endsection
