<?php

namespace Tests\AvtoDev\DevTools\Laravel\VarDumper;

use AvtoDev\DevTools\Laravel\VarDumper\DumpStack;
use AvtoDev\DevTools\Laravel\VarDumper\ServiceProvider;
use AvtoDev\DevTools\Laravel\VarDumper\VarDumperMiddleware;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

/**
 * Class ServiceProviderTest
 * @package Tests\AvtoDev\DevTools\Laravel\VarDumper
 */
class ServiceProviderTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplicationTrait;

    /**
     * @return void
     */
    public function testMiddlewareIsRegistered(): void
    {
        static::assertTrue($this->app->make(HttpKernel::class)->hasMiddleware(VarDumperMiddleware::class));
    }

    /**
     * @return void
     */
    public function testServiceContainers(): void
    {
        static::assertInstanceOf(DumpStack::class, $this->app->make(DumpStack::class));
    }

    /**
     * @param Application $app
     * @return void
     */
    protected function afterApplicationBootstrapped(Application $app): void
    {
        $app->register(ServiceProvider::class);
    }
}
