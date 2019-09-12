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
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertEmptyArray($value, string $message = ''): void
    {
        $this->assertIsArray($value, $message);
        $this->assertEmpty($value, $message);
    }

    /**
     * Asserts that value(s) is not empty array.
     *
     * @param mixed|array $value
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertNotEmptyArray($value, string $message = ''): void
    {
        $this->assertIsArray($value, $message);
        $this->assertNotEmpty($value, $message);
    }

    /**
     * Asserts that value(s) is empty string.
     *
     * @param mixed[]|string $values
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertEmptyString($values, string $message = ''): void
    {
        $values = \is_array($values) && $values !== []
            ? $values
            : [$values];

        foreach ($values as $value) {
            $this->assertIsString($value, $message);
            $this->assertEmpty($value, $message);
        }
    }

    /**
     * Asserts that value(s) is not empty string.
     *
     * @param mixed|string|string[] $values
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertNotEmptyString($values, string $message = ''): void
    {
        $values = \is_array($values) && $values !== []
            ? $values
            : [$values];

        foreach ($values as $value) {
            $this->assertIsString($value, $message);
            $this->assertNotEmpty($value, $message);
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
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertStringsEquals($expected, $actual, bool $ignore_case = true, string $message = ''): void
    {
        if ($ignore_case === true) {
            $expected = \mb_strtolower($expected, 'UTF-8');
            $actual = \mb_strtolower($actual, 'UTF-8');
        }

        $this->assertEquals($expected, $actual, $message === ''
            ? "String {$actual} does not equals {$expected}"
            : $message);
    }

    /**
     * Asserts that two strings is not equals each other.
     *
     * @param mixed|string $expected
     * @param mixed|string $actual
     * @param bool $ignore_case
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertStringsNotEquals($expected, $actual, bool $ignore_case = true, string $message = ''): void
    {
        if ($ignore_case === true) {
            $expected = \mb_strtolower($expected, 'UTF-8');
            $actual = \mb_strtolower($actual, 'UTF-8');
        }

        $this->assertNotEquals($expected, $actual, $message === ''
            ? "String equals ({$actual})"
            : $message);
    }

    /**
     * Asserts that passed class or interface name(s) are presents.
     *
     * @param string|string[] $class_names
     * @param bool $include_interfaces
     * @param string $message
     *
     * @return void
     * @throws ExpectationFailedException
     *
     * @throws InvalidArgumentException
     */
    public function assertClassExists($class_names, bool $include_interfaces = true, string $message = ''): void
    {
        foreach ((array)$class_names as $class_name) {
            $this->assertTrue(
                $include_interfaces === true
                    ? \class_exists($class_name) || \interface_exists($class_name)
                    : \class_exists($class_name),
                $message === ''
                    ? "Class {$class_name} was not found"
                    : $message
            );
        }
    }

    /**
     * Asserts that the class method(s) exists.
     *
     * @param object|string $object_or_class_name
     * @param string|string[] $expected_methods
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws ExpectationFailedException
     */
    public function assertHasMethods($object_or_class_name, $expected_methods, string $message = ''): void
    {
        foreach ((array)$expected_methods as $method_name) {
            $this->assertTrue(
                \method_exists($object_or_class_name, $method_name), $message === ''
                ? "Has no method named {$method_name}"
                : $message
            );
        }
    }

    /**
     * Asserts that passed class uses expected traits.
     *
     * @param string $class
     * @param string|string[] $expected_traits
     * @param string $message
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws ExpectationFailedException
     */
    public function assertClassUsesTraits($class, $expected_traits, string $message = ''): void
    {
        /**
         * Returns all traits used by a trait and its traits.
         *
         * @param string $trait
         *
         * @return string[]
         */
        $trait_uses_recursive = static function ($trait) use (&$trait_uses_recursive) {
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
        $class_uses_recursive = static function ($class) use ($trait_uses_recursive) {
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

        $uses = array_flip($uses);

        foreach ((array)$expected_traits as $k => $trait_class) {
            static::assertArrayHasKey($trait_class, $uses, $message === ''
                ? 'Class does not uses passed traits'
                : $message);
        }
    }

    /**
     * Assert that the array has a given structure.
     *
     * @param array $structure
     * @param array $testing_array
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public function assertArrayStructure(array $structure, $testing_array): void
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

    /**
     * Assert that the JSON-encoded array has a given structure.
     *
     * @param array $structure
     * @param string $json_string
     * @param int $encode_options
     *
     * @return void
     * @throws InvalidArgumentException
     *
     * @throws AssertionFailedError
     */
    public function assertJsonStructure(array $structure, $json_string, int $encode_options = 0): void
    {
        $this->assertIsString($json_string);

        $testing_array = \json_decode($json_string, true, 512, $encode_options);

        if (\json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Passed string has not valid JSON format.');
        }

        if (!\is_array($testing_array)) {
            throw new InvalidArgumentException('Passed data is not array.');
        }

        $this->assertArrayStructure($structure, $testing_array);
    }
}
