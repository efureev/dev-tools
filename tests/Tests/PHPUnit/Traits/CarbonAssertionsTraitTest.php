<?php

declare(strict_types = 1);

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits;

use Carbon\Carbon;
use Tests\AvtoDev\DevTools\AbstractTestCase;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CarbonAssertionsTrait;

/**
 * @covers \AvtoDev\DevTools\Tests\PHPUnit\Traits\CarbonAssertionsTrait<extended>
 */
class CarbonAssertionsTraitTest extends AbstractTestCase
{
    use CarbonAssertionsTrait;

    /**
     * Test assert with positive results.
     *
     * @return void
     */
    public function testAssertCarbonParseEqualsPositive(): void
    {
        $dt_sec = \DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2009 00:00:00');
        $dt     = \DateTime::createFromFormat('j-M-Y', '15-Feb-2009');
        $now    = Carbon::now();

        static::assertCarbonParseEquals($dt_sec->format('Y-m-d'), $dt_sec); // time ignoring false by default
        static::assertCarbonParseEquals($dt->format('Y-m-d'), $dt, true);
        static::assertCarbonParseEquals($now->format('Y-m-d'), $now, true);

        static::assertCarbonParseEquals($now->format('Y-m-d'), (clone $now)->addMinutes(10), true);

        static::assertCarbonParseEquals('2012-10-30', Carbon::create(2012, 10, 30), true);
        static::assertCarbonParseEquals('2012-10-30 12:24', Carbon::create(2012, 10, 30, 12, 24));
    }

    /**
     * Test assert with negative results.
     *
     * @return void
     */
    public function testAssertCarbonParseEqualsNegative(): void
    {
        static::assertTrue($this->assertAssertationInsideClosureFailed(function () {
            $dt = \DateTime::createFromFormat('j-M-Y', '15-Feb-2009');

            static::assertCarbonParseEquals($dt->format('Y-m-d'), $dt);
        }));

        static::assertTrue($this->assertAssertationInsideClosureFailed(function () {
            $now = Carbon::now();

            static::assertCarbonParseEquals($now->format('Y-m-d'), $now);
        }));

        static::assertTrue($this->assertAssertationInsideClosureFailed(function () {
            $now = Carbon::now();

            static::assertCarbonParseEquals($now->format('Y-m-d'), (clone $now)->addDays(10), true);
        }));
    }
}
