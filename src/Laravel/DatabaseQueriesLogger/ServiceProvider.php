<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Laravel\DatabaseQueriesLogger;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package AvtoDev\DevTools\Laravel\DatabaseQueriesLogger
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register service and listener.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->make('events')->listen(QueryExecuted::class, QueryExecutedEventsListener::class);
    }
}
