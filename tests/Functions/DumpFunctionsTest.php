<?php

declare(strict_types=1);

namespace Tests\AvtoDev\DevTools\Functions;

use AvtoDev\DevTools\Exceptions\VarDumperException;
use AvtoDev\DevTools\Laravel\VarDumper\DumpStackInterface;
use AvtoDev\DevTools\Laravel\VarDumper\ServiceProvider;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;

/**
 * Class DumpFunctionsTest
 * @package Tests\AvtoDev\DevTools\Functions
 */
class DumpFunctionsTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplicationTrait;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Required for '\dev\dump()' function testing
        $this->app->register(ServiceProvider::class);

        unset($_SERVER['DEV_DUMP_CLI_MODE']);

        \putenv('RR_HTTP=');
    }

    /**
     * @return void
     */
    public function testRanUsingCliFunction(): void
    {
        static::assertTrue(\function_exists('\\dev\\ran_using_cli'));

        static::assertTrue(\dev\ran_using_cli());

        // Detect running under RoadRunner (since RR v1.2.1)
        \putenv('RR_HTTP=true');
        static::assertFalse(\dev\ran_using_cli());
        \putenv('RR_HTTP=');

        $_SERVER['DEV_DUMP_CLI_MODE'] = true;
        static::assertTrue(\dev\ran_using_cli());

        $_SERVER['DEV_DUMP_CLI_MODE'] = false;
        static::assertFalse(\dev\ran_using_cli());
    }

    /**
     * @return void
     */
    public function testDdFunctionExists(): void
    {
        static::assertTrue(\function_exists('\\dev\\dd'));
    }

    /**
     * @return void
     * @throws VarDumperException
     * @throws \Exception
     */
    public function testDdFunctionThrowAnExceptionInNonCliMode(): void
    {
        $value1 = 'foo_' . \random_int(1, 255);
        $value2 = 'bar_' . \random_int(1, 255);

        $this->expectException(VarDumperException::class);
        $this->expectExceptionMessageRegExp("~${value1}.*${value2}~s");

        $_SERVER['DEV_DUMP_CLI_MODE'] = false;

        \dev\dd($value1, $value2);
    }

    /**
     * @return void
     */
    public function testDumpFunctionExists(): void
    {
        static::assertTrue(\function_exists('\\dev\\dump'));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testDumpFunctionPushIntoStackInNonCliMode(): void
    {
        $_SERVER['DEV_DUMP_CLI_MODE'] = false;

        /** @var DumpStackInterface $stack */
        $stack = $this->app->make(DumpStackInterface::class);

        \dev\dump($value1 = 'foo_' . \random_int(1, 255), $value2 = 'bar_' . \random_int(1, 255));

        static::assertCount(2, $stack);
        static::assertRegExp("~${value1}~s", $stack->all()[0]);
        static::assertRegExp("~${value2}~s", $stack->all()[1]);
    }
}
