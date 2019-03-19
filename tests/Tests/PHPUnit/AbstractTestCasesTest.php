<?php

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit;

use AvtoDev\DevTools\Tests\PHPUnit\Traits\AdditionalAssertionsTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CarbonAssertionsTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\InstancesAccessorsTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelCommandsAssertionsTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelEventsAssertionsTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelLogFilesAssertsTrait;
use Tests\AvtoDev\DevTools\AbstractTestCase;

/**
 * Class AbstractTestCasesTest
 * @package Tests\AvtoDev\DevTools\Tests\PHPUnit
 */
class AbstractTestCasesTest extends AbstractTestCase
{
    use AdditionalAssertionsTrait;

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAbstractTestCase(): void
    {
        $instance = new class extends \AvtoDev\DevTools\Tests\PHPUnit\AbstractTestCase
        {
        };

        static::assertInstanceOf(\PHPUnit\Framework\TestCase::class, $instance);

        static::assertClassUsesTraits($instance, [
            AdditionalAssertionsTrait::class,
            InstancesAccessorsTrait::class,
            CarbonAssertionsTrait::class,
        ]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAbstractLaravelTestCase(): void
    {
        $instance = new class extends \AvtoDev\DevTools\Tests\PHPUnit\AbstractLaravelTestCase
        {
            use CreatesApplicationTrait;
        };

        static::assertInstanceOf(\PHPUnit\Framework\TestCase::class, $instance);
        static::assertInstanceOf(\Illuminate\Foundation\Testing\TestCase::class, $instance);

        static::assertClassUsesTraits($instance, [
            AdditionalAssertionsTrait::class,
            InstancesAccessorsTrait::class,
            CreatesApplicationTrait::class,
            LaravelEventsAssertionsTrait::class,
            LaravelLogFilesAssertsTrait::class,
            LaravelCommandsAssertionsTrait::class,
            CarbonAssertionsTrait::class,
        ]);
    }
}
