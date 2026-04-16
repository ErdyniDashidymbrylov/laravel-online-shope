@extends('admin.layout')

@section('title', 'Dashboard')
@section('page_title', 'Панель администратора')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="admin-card p-3">
                <div class="text-muted small">Товаров в каталоге</div>
                <div class="fs-3 fw-semibold">{{ $productsCount }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="admin-card p-3">
                <div class="text-muted small">Ролей в системе</div>
                <div class="fs-3 fw-semibold">{{ $roles->count() }}</div>
            </div>
        </div>
    </div>

    <div class="admin-card p-3 mb-4">
        <h2 class="h5 mb-3">Добавить роль</h2>

        <form method="POST" action="{{ route('admin.roles.store') }}" class="row g-2">
            @csrf
            <div class="col-12 col-md-8">
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Например: Контент-менеджер"
                       required>
            </div>
            <div class="col-12 col-md-4 d-grid">
                <button type="submit" class="btn btn-primary">Создать роль</button>
            </div>
        </form>
    </div>

    <div class="admin-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Таблица ролей</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Slug</th>
                    <th>Пользователей</th>
                    <th class="text-end">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.roles.update', $role) }}"
                                  class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="name" value="{{ $role->name }}" class="form-control form-control-sm" required>
                                <button type="submit" class="btn btn-outline-primary btn-sm">Сохранить</button>
                            </form>
                        </td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td>{{ $role->users_count }}</td>
                        <td class="text-end">
                            <form method="POST"
                                  action="{{ route('admin.roles.destroy', $role) }}"
                                  onsubmit="return confirm('Удалить роль {{ $role->name }}?');"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Ролей пока нет</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
