<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Tests\PHPUnit;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;

/**
 * Class AbstractLaravelTestCase
 * @package AvtoDev\DevTools\Tests\PHPUnit
 */
abstract class AbstractLaravelTestCase extends TestCase
{
    use Traits\AdditionalAssertionsTrait,
        Traits\InstancesAccessorsTrait,
        Traits\CreatesApplicationTrait,
        Traits\LaravelLogFilesAssertsTrait,
        Traits\LaravelEventsAssertionsTrait,
        Traits\LaravelCommandsAssertionsTrait,
        Traits\CarbonAssertionsTrait;

    /**
     * {@inheritdoc}
     */
    protected function setUpTraits(): array
    {
        $uses = parent::setUpTraits();

        if (isset($uses[Traits\WithDatabaseQueriesLogging::class])) {
            $this->enableDatabaseQueriesLogging();
        }

        if (isset($uses[Traits\WithDatabaseDisconnects::class])) {
            $this->enableDatabaseDisconnects();
        }

        if (isset($uses[Traits\WithMemoryClean::class])) {
            $this->enableCleanMemory();
        }

        return $uses;
    }

    /**
     * Make some before application bootstrapped (call `$app->useStoragePath(...)`, `$app->loadEnvironmentFrom(...)`,
     * etc).
     *
     * @see \AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait::createApplication
     *
     * @param Application $app
     */
    protected function beforeApplicationBootstrapped(Application $app): void
    {
        //
    }

    /**
     * Make some after application bootstrapped (register your service-providers `$app->register(...)`, etc).
     *
     * @see \AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait::createApplication
     *
     * @param Application $app
     */
    protected function afterApplicationBootstrapped(Application $app): void
    {
        //
    }
}
