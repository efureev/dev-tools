<?php

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits;

use AvtoDev\DevTools\Tests\PHPUnit\AbstractLaravelTestCase;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\InstancesAccessorsTrait;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Tests\AvtoDev\DevTools\AbstractTestCase;

/**
 * Class InstancesAccessorsTraitTest
 * @package Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
class InstancesAccessorsTraitTest extends AbstractTestCase
{
    use InstancesAccessorsTrait;

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     *
     * @return void
     */
    public function testsTraitAsserts(): void
    {
        $instance = new class
        {
            private $property = 'foo';

            private function method()
            {
                return 'bar';
            }
        };

        static::assertEquals('foo', static::getProperty($instance, 'property'));
        static::assertEquals('bar', static::callMethod($instance, 'method'));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetClosureHash(): void
    {
        $test_cases = [
            function () {
            },
            function () {
                return new class extends AbstractLaravelTestCase
                {
                };
            },
            function () {
                static::resetCount();
            },
        ];

        static::assertNotEmpty($test_cases);

        foreach ($test_cases as $test_case) {
            $hash = static::getClosureHash($test_case);

            static::assertGreaterThanOrEqual(8, \mb_strlen($hash));

            // Second call for a closure
            static::assertEquals($hash, static::getClosureHash($test_case));
        }
    }
}
