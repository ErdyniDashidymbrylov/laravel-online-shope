@extends('admin.layout')

@section('title', 'Панель администратора')
@section('page_title', 'Панель администратора')

@section('content')
    <div class="admin-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h5 mb-0">Продажи за текущую неделю</h2>
            <span class="text-muted small">{{ \Carbon\Carbon::parse($report['period']['from'])->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($report['period']['to'])->format('d.m.Y') }}</span>
        </div>
        <p class="text-muted small mb-3">Оплаченные заказы по дате оформления</p>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="text-muted small">Продаж</div>
                <div class="fs-4 fw-semibold">{{ $report['total_sales'] }}</div>
            </div>
            <div class="col-12 col-md-4">
                <div class="text-muted small">Выручка</div>
                <div class="fs-4 fw-semibold">{{ number_format($report['total_revenue'], 2, ',', ' ') }} ₽</div>
            </div>
            <div class="col-12 col-md-4">
                <div class="text-muted small">Средний чек</div>
                <div class="fs-4 fw-semibold">{{ number_format($report['average_check'], 2, ',', ' ') }} ₽</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">Дата</th>
                    <th scope="col" class="text-end">Продаж</th>
                    <th scope="col" class="text-end">Выручка</th>
                </tr>
                </thead>
                <tbody>
                @foreach($report['daily_sales'] as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['date'])->locale('ru')->translatedFormat('d.m.Y, l') }}</td>
                        <td class="text-end">{{ $day['sales_count'] }}</td>
                        <td class="text-end">{{ number_format($day['revenue'], 2, ',', ' ') }} ₽</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card p-3 mb-4">
        <h2 class="h5 mb-3">Дневные отчеты за последние 7 дней</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">Дата</th>
                    <th scope="col" class="text-end">Заказов</th>
                    <th scope="col" class="text-end">Продаж</th>
                    <th scope="col" class="text-end">Отменено</th>
                    <th scope="col" class="text-end">Выручка</th>
                </tr>
                </thead>
                <tbody>
                @forelse($recentReports as $dailyReport)
                    <tr>
                        <td>{{ $dailyReport->report_date->format('d.m.Y') }}</td>
                        <td class="text-end">{{ $dailyReport->orders_count }}</td>
                        <td class="text-end">{{ $dailyReport->sales_count }}</td>
                        <td class="text-end">{{ $dailyReport->canceled_count }}</td>
                        <td class="text-end">{{ number_format((float) $dailyReport->revenue, 2, ',', ' ') }} ₽</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Отчеты еще не рассчитаны</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
