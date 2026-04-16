@extends('admin.layout')

@section('title', $product->name)
@section('page_title', 'Карточка товара')

@section('content')
    <div class="admin-card p-3">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h2 class="h4 mb-1">{{ $product->name }}</h2>
                <div class="text-muted">ID: {{ $product->id }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">Редактировать</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Назад</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="border rounded p-3 bg-light-subtle h-100">
                    <div class="small text-muted">Категория</div>
                    <div class="fw-semibold">{{ $product->category?->name ?? '—' }}</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="border rounded p-3 bg-light-subtle h-100">
                    <div class="small text-muted">Цена</div>
                    <div class="fw-semibold">{{ number_format((float) $product->price, 2, '.', ' ') }}</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="border rounded p-3 bg-light-subtle h-100">
                    <div class="small text-muted">Остаток</div>
                    <div class="fw-semibold">{{ $product->stock }}</div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="small text-muted mb-1">Описание</div>
            <div>{{ $product->description ?: 'Описание не заполнено' }}</div>
        </div>
    </div>
@endsection
