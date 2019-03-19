<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Tests\PHPUnit\Traits;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Application;

/**
 * Trait CreatesApplicationTrait
 * @package AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
trait CreatesApplicationTrait
{
    /**
     * Get application bootstrap file(s).
     *
     * @return string|array
     */
    public function getApplicationBootstrapFiles()
    {
        return [
            __DIR__ . '/../bootstrap/app.php',
            __DIR__ . '/../../../../../../../bootstrap/app.php',
            __DIR__ . '/../../../../vendor/laravel/laravel/bootstrap/app.php',
            __DIR__ . '/../../../../../../laravel/laravel/bootstrap/app.php',
        ];
    }

    /**
     * Creates the application.
     *
     * @throws FileNotFoundException
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        foreach ((array)$this->getApplicationBootstrapFiles() as $path) {
            if (\file_exists($path)) {
                /** @var Application $app */
                $app = require $path;

                if (\method_exists($this, $before_method_name = 'beforeApplicationBootstrapped')) {
                    $this->$before_method_name($app);
                }

                $app->make(Kernel::class)->bootstrap();

                if (\method_exists($this, $after_method_name = 'afterApplicationBootstrapped')) {
                    $this->$after_method_name($app);
                }

                return $app;
            }
        }

        throw new FileNotFoundException('Application bootstrap file was not found');
    }
}
