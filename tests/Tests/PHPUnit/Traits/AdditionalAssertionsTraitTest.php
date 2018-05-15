<?php

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits\Stubs\TraitOne;
use Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits\Stubs\TraitThree;
use Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits\Stubs\TraitTwo;

/**
 * Class AdditionalAssertionsTraitTest.
 */
class AdditionalAssertionsTraitTest extends AbstractTraitTestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function testsTraitAsserts()
    {
        /** @see AdditionalAssertionsTrait::assertIsNumeric */
        $this->makeAssertTest('assertIsNumeric', [1, 1.0, 0.00001, '1', '1.0', '0.00001'], ['foo', null]);

        /** @see AdditionalAssertionsTrait::assertIsArray */
        $this->makeAssertTest('assertIsArray', [[], [null], [1], [1, 2], [1, [null]]], ['foo', 1, new \stdClass]);

        /** @see AdditionalAssertionsTrait::assertNotEmptyArray */
        $this->makeAssertTest('assertNotEmptyArray', [[1], ['foo'], [new \stdClass]], [[]]);

        /** @see AdditionalAssertionsTrait::assertEmptyArray */
        $this->makeAssertTest('assertEmptyArray', [[]], ['foo', [1], new \stdClass, [[]]]);

        /** @see AdditionalAssertionsTrait::assertIsString */
        $this->makeAssertTest('assertIsString', ['foo', 'bar'], [null, 1, new class
        {
            public function __toString()
            {
                return 'baz';
            }
        }]);

        /** @see AdditionalAssertionsTrait::assertEmptyString */
        $this->makeAssertTest('assertEmptyString', [''], ['foo', [1], new \stdClass, []]);

        /** @see AdditionalAssertionsTrait::assertNotEmptyString */
        $this->makeAssertTest('assertNotEmptyString', ['foo'], ['', null]);

        /** @see AdditionalAssertionsTrait::assertStringsEquals */
        $this->makeAssertTest('assertStringsEquals', ['Превед foo'], [], 'превед foo', true);
        $this->makeAssertTest('assertStringsEquals', ['превед foo'], [], 'превед foo', false);

        /** @see AdditionalAssertionsTrait::assertStringsNotEquals */
        $this->makeAssertTest('assertStringsNotEquals', ['Превед foo'], [], 'bar', true);
        $this->makeAssertTest('assertStringsNotEquals', ['превед foo'], [], 'Превед foo', false);

        /** @see AdditionalAssertionsTrait::assertClassExists */
        $this->makeAssertTest('assertClassExists', [\Exception::class, \Throwable::class], ['FooClass']);
        $this->makeAssertTest('assertClassExists', [\Exception::class], ['FooClass', \Throwable::class], false);

        /** @see AdditionalAssertionsTrait::assertHasMethods */
        $this->makeAssertTest('assertHasMethods', [\Exception::class], [\Throwable::class], '__wakeup');
        $this->makeAssertTest('assertHasMethods', [\Exception::class], [\Throwable::class], ['__wakeup', '__clone']);

        /** @see AdditionalAssertionsTrait::assertClassUsesTraits */
        $test_instance = new class {
            use TraitOne, TraitTwo;
        };
        $this->makeAssertTest('assertClassUsesTraits', [$test_instance], [new \stdClass], [
            TraitOne::class, TraitTwo::class, TraitThree::class,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed|\PHPUnit\Framework\TestCase
     */
    protected function classUsedTraitFactory()
    {
        return new class extends \PHPUnit\Framework\TestCase
        {
            use \AvtoDev\DevTools\Tests\PHPUnit\Traits\AdditionalAssertionsTrait;
        };
    }
}
