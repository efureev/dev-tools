<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Laravel\VarDumper;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package AvtoDev\DevTools\Laravel\VarDumper
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register service and listener.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->make(Kernel::class)->pushMiddleware(VarDumperMiddleware::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(DumpStackInterface::class, DumpStack::class);
    }
}
