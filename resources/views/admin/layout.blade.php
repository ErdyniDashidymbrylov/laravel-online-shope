<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Здесь обычно подключаются стили -->
</head>
<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Admin Panel</a>

    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link nav-link">Sign out</button>
            </form>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <div class="list-group">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                        Dashboard
                    </a>
{{--                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">--}}
{{--                        Users--}}
{{--                    </a>--}}
{{--                    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">--}}
{{--                        Products--}}
{{--                    </a>--}}
{{--                    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">--}}
{{--                        Orders--}}
{{--                    </a>--}}
                </div>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="mt-3">
                {{-- Вывод уведомлений об успехе --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Вывод уведомлений об ошибках --}}
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Сюда будет подставляться контент других страниц --}}
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
