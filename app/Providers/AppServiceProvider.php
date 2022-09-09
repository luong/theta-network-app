<?php

namespace App\Providers;

use App\Services\DexService;
use App\Services\OnChainService;
use App\Services\SystemService;
use App\Services\TdropContract;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        OnChainService::class => OnChainService::class,
        ThetaService::class => ThetaService::class,
        MessageService::class => MessageService::class,
        TdropContract::class => TdropContract::class,
        SystemService::class => SystemService::class,
        DexService::class => DexService::class
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
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }
}
