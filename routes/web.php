<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\YooKassaController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->middleware('guest')
    ->group(function (): void {
        Route::get('/register', 'showRegistrationForm')->name('register.form');
        Route::post('/register', 'register')->name('register');

        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.submit');
    });

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(EmailVerificationController::class)->group(function (): void {
        Route::get('/email/verify', 'notice')->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', 'verify')
            ->middleware('signed')
            ->name('verification.verify');
        Route::post('/email/verification-notification', 'send')
            ->middleware('throttle:6,1')
            ->name('verification.send');
    });

    Route::controller(AuthController::class)
        ->middleware('verified')
        ->group(function (): void {
            Route::get('/profile', 'showProfile')->name('profile.form');
            Route::patch('/profile/{id}', 'updateProfile')->name('profile.update');

            Route::get('/change-password', 'showChangePasswordForm')->name('password.form');
            Route::post('/change-password', 'updatePassword')->name('password.update');
        });
});

Route::view('/', 'main')->name('home');

Route::controller(ProductController::class)->group(function (): void {
    Route::get('/products', 'index')->name('products.index');
    Route::get('/products/{product}', 'show')->name('products.show');
});

Route::controller(CategoryController::class)->group(function (): void {
    Route::get('/categories', 'index')->name('categories.index');
    Route::get('/categories/{category:slug}', 'show')->name('categories.show');
});

Route::controller(CartController::class)->group(function (): void {
    Route::get('/cart', 'index')->name('cart.index');
    Route::post('/cart/items/{product}', 'store')->name('cart.items.store');
    Route::patch('/cart/items/{product}', 'update')->name('cart.items.update');
    Route::delete('/cart/items/{product}', 'destroy')->name('cart.items.destroy');
    Route::delete('/cart', 'clear')->name('cart.clear');
});

Route::controller(OrderController::class)
    ->middleware(['auth', 'verified'])
    ->group(function (): void {
        Route::get('/orders', 'index')->name('orders.index');
        Route::post('/orders', 'store')->name('orders.store');
        Route::patch('/orders/{order}/status', 'updateStatus')->name('orders.status.update');
    });

Route::post('/payments/yookassa/webhook', [YooKassaController::class, 'webhook'])->name('yookassa.webhook');
Route::get('/payments/yookassa/return/{order}', [YooKassaController::class, 'return'])
    ->middleware(['auth', 'verified'])
    ->name('yookassa.return');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::controller(DashboardController::class)->group(function (): void {
            Route::get('/', 'index')->name('dashboard');
            Route::post('/roles', 'store')->name('roles.store');
            Route::patch('/roles/{role}', 'update')->name('roles.update');
            Route::delete('/roles/{role}', 'destroy')->name('roles.destroy');
        });

        Route::resource('products', AdminProductController::class);
        Route::resource('users', UserAdminController::class);

        Route::patch('users/{user}/password', [UserAdminController::class, 'resetPassword'])->name('users.password');

        Route::resource('orders', OrderAdminController::class);
    });
