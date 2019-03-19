<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Tests\PHPUnit\Traits;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Trait AdditionalAssertionsTrait
 * @package AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
trait AdditionalAssertionsTrait
{
    /**
     * Asserts that value(s) is empty array.
     *
     * @param mixed|array $value
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertEmptyArray($value): void
    {
        static::assertIsArray($value);
        static::assertEmpty($value);
    }

    /**
     * Asserts that value(s) is not empty array.
     *
     * @param mixed|array $value
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertNotEmptyArray($value): void
    {
        static::assertIsArray($value);
        static::assertNotEmpty($value);
    }

    /**
     * Asserts that value(s) is empty string.
     *
     * @param mixed[]|string $values
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertEmptyString($values): void
    {
        $values = \is_array($values) && $values !== []
            ? $values
            : [$values];

        foreach ($values as $value) {
            static::assertIsString($value);
            static::assertEmpty($value);
        }
    }

    /**
     * Asserts that value(s) is not empty string.
     *
     * @param mixed|string|string[] $values
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertNotEmptyString($values): void
    {
        $values = \is_array($values) && $values !== []
            ? $values
            : [$values];

        foreach ($values as $value) {
            static::assertIsString($value);
            static::assertNotEmpty($value);
        }
    }

    /**
     * Asserts that two strings is equals each other.
     *
     * `->assertEquals($ignore_case = true)` not used because it works not correctly with UTF-8 strings.
     *
     * @param mixed|string $expected
     * @param mixed|string $actual
     * @param bool $ignore_case
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertStringsEquals($expected, $actual, $ignore_case = true): void
    {
        if ($ignore_case === true) {
            $expected = \mb_strtolower($expected, 'UTF-8');
            $actual = \mb_strtolower($actual, 'UTF-8');
        }

        static::assertEquals($expected, $actual, "String {$actual} does not equals {$expected}");
    }

    /**
     * Asserts that two strings is not equals each other.
     *
     * @param mixed|string $expected
     * @param mixed|string $actual
     * @param bool $ignore_case
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertStringsNotEquals($expected, $actual, $ignore_case = true): void
    {
        if ($ignore_case === true) {
            $expected = \mb_strtolower($expected, 'UTF-8');
            $actual = \mb_strtolower($actual, 'UTF-8');
        }

        static::assertNotEquals($expected, $actual, "String equals ({$actual})");
    }

    /**
     * Asserts that passed class or interface name(s) are presents.
     *
     * @param string|string[] $class_names
     * @param bool $include_interfaces
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @return void
     */
    public static function assertClassExists($class_names, $include_interfaces = true): void
    {
        foreach ((array)$class_names as $class_name) {
            static::assertTrue(
                $include_interfaces === true
                    ? \class_exists($class_name) || \interface_exists($class_name)
                    : \class_exists($class_name),
                "Class {$class_name} was not found"
            );
        }
    }

    /**
     * Asserts that the class method(s) exists.
     *
     * @param object|string $object_or_class_name
     * @param string|string[] $expected_methods
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertHasMethods($object_or_class_name, $expected_methods): void
    {
        foreach ((array)$expected_methods as $method_name) {
            static::assertTrue(
                \method_exists($object_or_class_name, $method_name), "Has no method named {$method_name}"
            );
        }
    }

    /**
     * Asserts that passed class uses expected traits.
     *
     * @param string $class
     * @param string|string[] $expected_traits
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertClassUsesTraits($class, $expected_traits): void
    {
        /**
         * Returns all traits used by a trait and its traits.
         *
         * @param string $trait
         *
         * @return string[]
         */
        $trait_uses_recursive = function ($trait) use (&$trait_uses_recursive) {
            $traits = \class_uses($trait);

            $tt = [[]];
            foreach ($traits as $trait_iterate) {
                $tt[] = $trait_uses_recursive($trait_iterate);
            }

            $traits = \array_merge($traits, ...$tt);

            return $traits;
        };

        /**
         * Returns all traits used by a class, its subclasses and trait of their traits.
         *
         * @param object|string $class
         *
         * @return array
         */
        $class_uses_recursive = function ($class) use ($trait_uses_recursive) {
            if (\is_object($class)) {
                $class = \get_class($class);
            }

            $results = [[]];

            foreach (\array_reverse(\class_parents($class)) + [$class => $class] as $class_iterate) {
                $results[] = $trait_uses_recursive($class_iterate);
            }

            return \array_values(\array_merge(...$results));
        };

        $uses = $class_uses_recursive($class);

        foreach ((array)$expected_traits as $trait_class) {
            static::assertContains($trait_class, $uses);
        }
    }

    /**
     * Assert that the array has a given structure.
     *
     * @param array $structure
     * @param array $testing_array
     *
     * @throws InvalidArgumentException
     */
    public static function assertArrayStructure($structure, $testing_array): void
    {
        foreach ($structure as $key => $value) {
            if (\is_array($value)) {
                if ($key === '*') {
                    static::assertIsArray($testing_array);

                    foreach ($testing_array as $item) {
                        static::assertArrayStructure($structure['*'], $item);
                    }
                } else {
                    static::assertArrayHasKey($key, $testing_array);

                    static::assertArrayStructure($structure[$key], $testing_array[$key]);
                }
            } else {
                static::assertArrayHasKey($value, $testing_array);
            }
        }
    }
}
