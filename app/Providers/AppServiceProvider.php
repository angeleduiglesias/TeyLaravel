<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Documento;
use App\Observers\DocumentoObserver;

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
        Documento::observe(DocumentoObserver::class);
    }
}
