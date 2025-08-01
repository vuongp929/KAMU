<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\ClientLayoutComposer;

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
        // // Đăng ký View Composer cho tất cả các view trong thư mục 'clients'
        // // và cả layout 'layouts.client'.
        // // Dấu * có nghĩa là áp dụng cho tất cả các file trong thư mục đó.
        // View::composer(
        //     ['layouts.client', 'clients.*'], 
        //     ClientLayoutComposer::class
        // );
    }
}