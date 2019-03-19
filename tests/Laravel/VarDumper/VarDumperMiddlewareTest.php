<?php

namespace Tests\AvtoDev\DevTools\Laravel\VarDumper;

use AvtoDev\DevTools\Laravel\VarDumper\ServiceProvider;
use AvtoDev\DevTools\Laravel\VarDumper\VarDumperMiddleware;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;

/**
 * Class VarDumperMiddlewareTest
 * @package Tests\AvtoDev\DevTools\Laravel\VarDumper
 */
class VarDumperMiddlewareTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplicationTrait;

    protected const GLOBAL_VAR_CLI_MODE = 'DEV_DUMP_CLI_MODE';

    protected const CACHE_DIR = __DIR__ . '/../../temp';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER[static::GLOBAL_VAR_CLI_MODE] = false;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        unset($_SERVER[static::GLOBAL_VAR_CLI_MODE]);

        (new Filesystem)->cleanDirectory(static::CACHE_DIR);

        parent::tearDown();
    }

    /**
     * @throws \Exception
     */
    public function testDumpWithMiddlewareWorking(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $repeat_counter = 0;

        // Two requests will contains dump data, any another - not
        $router
            ->get($path = '/test' . \random_int(1, 255), function () use (&$repeat_counter) {
                if ($repeat_counter < 2) {
                    \dev\dump('foo bar', 'john doe');

                    $repeat_counter++;
                }

                return \response('<html><body>bar baz</body></html>');
            })->middleware(VarDumperMiddleware::class);

        $response1 = $this->get($path);
        $response2 = $this->get($path);
        $response3 = $this->get($path);

        foreach (['foo bar', 'john doe', 'bar baz', 'window.Sfdump'] as $item) {
            static::assertStringContainsString($item, $response1->getContent());
            static::assertStringContainsString($item, $response2->getContent());
        }

        foreach (['foo bar', 'john doe', 'window.Sfdump'] as $item) {
            static::assertStringNotContainsString($item, $response3->getContent());
        }
        static::assertStringContainsString('bar baz', $response3->getContent());
    }

    /**
     * @throws \Exception
     */
    public function testDdWithException(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        // \dev\dd() should work with middleware
        $router
            ->get($path_with_middleware = '/test' . \random_int(1, 255), function () {
                \dev\dd('foo bar');

                return \response('bar baz');
            })->middleware(VarDumperMiddleware::class);

        // and without it
        $router
            ->get($path_without_middleware = '/test' . \random_int(256, 512), function () {
                \dev\dd('foo bar');

                return \response('bar baz');
            });

        $response_with_middleware = $this->get($path_with_middleware);
        $response_without_middleware = $this->get($path_without_middleware);

        foreach ([$response_with_middleware, $response_without_middleware] as $response) {
            static::assertStringContainsString('foo bar', $response->getContent());
            static::assertStringContainsString('window.Sfdump', $response->getContent());
            static::assertStringNotContainsString('bar baz', $response->getContent());
        }
    }

    /**
     * @param Application $app
     */
    protected function afterApplicationBootstrapped(Application $app): void
    {
        $app['config']['cache.stores.file.path'] = static::CACHE_DIR;
        $app['config']['cache.default'] = 'file';

        $app->register(ServiceProvider::class);
    }
}
