<?php

declare(strict_types=1);

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits;

use AvtoDev\DevTools\Tests\PHPUnit\AbstractLaravelTestCase;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\WithMemoryClean;

/**
 * Class WithMemoryCleanTest
 * @package Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
class WithMemoryCleanTest extends AbstractLaravelTestCase
{
    use WithMemoryClean;

    /**
     * @var mixed property for testing
     */
    protected $test_property;

    /**
     * Shit func
     */
    public function testSomeShit(): void
    {
        $this->test_property = 'test';
        $this->clearMemory();
        static::assertNull($this->test_property);
    }

    /**
     * Test closure registration.
     *
     * @return void
     * @throws \Exception
     */
    public function testClosureRegistration(): void
    {
        $closure_hash = static::getClosureHash($this->cleanMemoryClosureFactory());
        $found = false;

        foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
            if (static::getClosureHash($callback) === $closure_hash) {
                $found = true;

                break;
            }
        }

        static::assertTrue($found, 'Closure is not registered on application destroyed');
    }
}
