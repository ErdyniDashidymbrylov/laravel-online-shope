@extends('admin.layout')

@section('title', 'Товары')
@section('page_title', 'Управление товарами')

@section('content')
    <div class="admin-card p-3">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex gap-2">
                <input type="text"
                       name="q"
                       value="{{ $q ?? '' }}"
                       class="form-control"
                       placeholder="Поиск по названию, описанию или ID">
                <button type="submit" class="btn btn-outline-primary">Найти</button>
            </form>

            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Добавить товар</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Остаток</th>
                    <th class="text-end">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td>{{ number_format((float) $product->price, 2, '.', ' ') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-secondary btn-sm">Открыть</a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">Редактировать</a>
                            <form method="POST"
                                  action="{{ route('admin.products.destroy', $product) }}"
                                  onsubmit="return confirm('Удалить товар {{ $product->name }}?');"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Товары не найдены</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
