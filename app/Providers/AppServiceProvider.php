<?php

namespace App\Providers;

use App\State\FeePaymentProcessor;
use App\State\FeeProcessor;
use App\State\ParkingPayProcessor;
use ApiPlatform\State\ProcessorInterface;
use Illuminate\Support\ServiceProvider;
use App\State\ParkingCheckProvider;
use ApiPlatform\State\ProviderInterface;
use Illuminate\Contracts\Foundation\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ParkingCheckProvider::class, function (Application $app) {
            return new ParkingCheckProvider();
        });

        $this->app->tag(
            [
                ParkingCheckProvider::class,
                ParkingPayProcessor::class,
                FeeProcessor::class,
                FeePaymentProcessor::class,
            ],
            ProviderInterface::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
		$this->app->tag(ParkingPayProcessor::class, ProcessorInterface::class);

		$this->app->tag(FeeProcessor::class, ProcessorInterface::class);

		$this->app->tag(FeePaymentProcessor::class, ProcessorInterface::class);
    }
}
