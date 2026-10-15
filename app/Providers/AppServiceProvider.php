<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\SendWelcomeEmailAfterVerification;
use App\Models\Product;
use App\Observers\ProductSearchObserver;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Verified::class, SendWelcomeEmailAfterVerification::class);
        Product::observe(ProductSearchObserver::class);
    }
}
