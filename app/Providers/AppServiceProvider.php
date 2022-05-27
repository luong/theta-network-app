<?php

namespace App\Providers;

use App\Services\OnChainService;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        OnChainService::class => OnChainService::class,
        ThetaService::class => ThetaService::class,
        MessageService::class => MessageService::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
