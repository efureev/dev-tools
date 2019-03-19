<?php

namespace Tests\AvtoDev\DevTools\Laravel\DatabaseQueriesLogger;

use AvtoDev\DevTools\Laravel\DatabaseQueriesLogger\QueryExecutedEventsListener;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelLogFilesAssertsTrait;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

/**
 * Class QueryExecutedEventsListenerTest
 * @package Tests\AvtoDev\DevTools\Laravel\DatabaseQueriesLogger
 */
class QueryExecutedEventsListenerTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplicationTrait,
        LaravelLogFilesAssertsTrait;

    /**
     * @var QueryExecutedEventsListener
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new QueryExecutedEventsListener($this->app->make(LoggerInterface::class));

        $this->clearLaravelLogs();
    }

    /**
     * Test log level getter.
     *
     * @return void
     */
    public function testLoggingLevel(): void
    {
        static::assertEquals('debug', $this->instance->loggingLevel());

        \putenv('DATABASE_QUERIES_LOGGING_LEVEL=warning');
        static::assertEquals('warning', $this->instance->loggingLevel());

        // Unset back
        \putenv('DATABASE_QUERIES_LOGGING_LEVEL');
    }

    /**
     * Test handel method.
     *
     * @return void
     */
    public function testHandle(): void
    {
        $event = new QueryExecuted(
            $sql = 'select * from users_' . Str::random(),
            [$a = 'foo ' . Str::random() => $b = 'bar' . Str::random()],
            time(),
            $this->app->make('db')->connection()
        );

        $this->instance->handle($event);

        $this->assertLogFileContains("Database query [$sql]");
        $this->assertLogFileContains($a);
        $this->assertLogFileContains($b);
    }

    /**
     * Test handel method with passing datatine.
     *
     * @throws \Exception
     */
    public function testHandleWithDataTime(): void
    {
        $event = new QueryExecuted(
            $sql = 'select * from users_' . Str::random(),
            [$a = 'foo ' . Str::random() => $b = 'bar' . Str::random(), $datetime = new \DateTime('now')],
            time(),
            $this->app->make('db')->connection()
        );

        $this->instance->handle($event);

        $this->assertLogFileContains("Database query [$sql]");
        $this->assertLogFileContains($datetime->format('Y-m-d H:i:s'));
        $this->assertLogFileContains($a);
        $this->assertLogFileContains($b);
    }
}
