<?php

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits;

use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelEventsAssertionsTrait;
use Illuminate\Support\Facades\Event;

/**
 * Class LaravelEventsAssertionsTraitTest
 * @package Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
class LaravelEventsAssertionsTraitTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplicationTrait,
        LaravelEventsAssertionsTrait;

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \ReflectionException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testTrait(): void
    {
        $event = 'foo.event';
        $listener = new class
        {
        };

        static::assertEventHasNoListener($event, $listener);

        Event::listen($event, \get_class($listener));

        static::assertEventHasListener($event, $listener);
    }
}
