<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Админка') - Laravel Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-bg: #f5f7fb;
            --admin-sidebar: #1f2937;
            --admin-sidebar-link: #d1d5db;
            --admin-sidebar-link-active: #ffffff;
            --admin-sidebar-active-bg: #2563eb;
            --admin-card-border: #e5e7eb;
        }

        body {
            background: var(--admin-bg);
        }

        .admin-shell {
            min-height: 100vh;
        }

        .admin-sidebar {
            background: var(--admin-sidebar);
            color: #fff;
            min-height: 100vh;
        }

        .admin-brand {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .admin-nav-link {
            display: block;
            padding: .55rem .75rem;
            border-radius: .5rem;
            color: var(--admin-sidebar-link);
            text-decoration: none;
            transition: .15s ease-in-out;
        }

        .admin-nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        .admin-nav-link.active {
            background: var(--admin-sidebar-active-bg);
            color: var(--admin-sidebar-link-active);
        }

        .admin-content {
            padding: 1.25rem;
        }

        .admin-card {
            border: 1px solid var(--admin-card-border);
            border-radius: .9rem;
            background: #fff;
            box-shadow: 0 8px 26px rgba(16, 24, 40, .05);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container-fluid admin-shell">
    <div class="row">
        <aside class="col-12 col-lg-2 p-3 admin-sidebar">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none admin-brand">Admin Panel</a>
            </div>

            <nav class="d-grid gap-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="admin-nav-link @if(request()->routeIs('admin.dashboard')) active @endif">
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="admin-nav-link @if(request()->routeIs('admin.products.*')) active @endif">
                    Products
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="admin-nav-link @if(request()->routeIs('admin.users.*')) active @endif">
                    Users
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="admin-nav-link @if(request()->routeIs('admin.orders.*')) active @endif">
                    Orders
                </a>
            </nav>

            <hr class="border-secondary my-4">

            <form action="{{ route('logout') }}" method="POST" class="d-grid">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Выход</button>
            </form>
        </aside>

        <main class="col-12 col-lg-10 admin-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h4 mb-0">@yield('page_title', 'Панель администратора')</h1>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">На сайт</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
