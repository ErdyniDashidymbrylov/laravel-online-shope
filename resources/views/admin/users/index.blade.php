@extends('admin.layout')

@section('title', 'Пользователи')
@section('page_title', 'Управление пользователями')

@section('content')
    <div class="admin-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Пользователи</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Создать пользователя</a>
        </div>

        @if($users->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Создан</th>
                        <th class="text-end">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>{{ $user->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary btn-sm">Просмотр</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">Редактировать</a>

                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Удалить пользователя?')">
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
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted mb-0">Пользователей пока нет.</p>
        @endif
    </div>
@endsection
