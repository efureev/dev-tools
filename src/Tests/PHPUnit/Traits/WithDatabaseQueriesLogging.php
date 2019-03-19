<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Tests\PHPUnit\Traits;

use AvtoDev\DevTools\Laravel\DatabaseQueriesLogger\QueryExecutedEventsListener;
use Illuminate\Database\Events\QueryExecuted;
use Psr\Log\LoggerInterface;

/**
 * Trait WithDatabaseQueriesLogging
 * @package AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
trait WithDatabaseQueriesLogging
{
    /**
     * Database queries logger instance getter.
     *
     * @return LoggerInterface
     */
    public function databaseQueriesLoggerInstance(): LoggerInterface
    {
        return $this->app->make(LoggerInterface::class);
    }

    /**
     * Enable database queries logging for this test class.
     *
     * @throws \Exception
     */
    public function enableDatabaseQueriesLogging(): void
    {
        $this->afterApplicationCreated(function () {
            $this->app->make('events')->listen(QueryExecuted::class, function (QueryExecuted $event) {
                (new QueryExecutedEventsListener($this->databaseQueriesLoggerInstance()))->handle($event);
            });
        });
    }
}
