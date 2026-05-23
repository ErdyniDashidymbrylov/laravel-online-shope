@extends('layouts.app')

@section('title', 'Подтверждение Email')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-3">Подтвердите ваш email</h1>
                        <p class="text-muted mb-4">
                            Мы отправили ссылку подтверждения на вашу почту.
                            Перейдите по ней, чтобы активировать аккаунт.
                        </p>

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Отправить письмо повторно
                            </button>
                        </form>

                        <div class="mt-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">На главную</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
