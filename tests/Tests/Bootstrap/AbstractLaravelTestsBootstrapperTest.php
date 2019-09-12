<?php

declare(strict_types = 1);

namespace Tests\AvtoDev\DevTools\Tests\Bootstrap;

use Exception;
use Tests\AvtoDev\DevTools\AbstractTestCase;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use AvtoDev\DevTools\Tests\Bootstrap\AbstractLaravelTestsBootstrapper;

/**
 * Class AbstractLaravelTestsBootstrapperTest
 * @package Tests\AvtoDev\DevTools\Tests\Bootstrap
 * @covers \AvtoDev\DevTools\Tests\Bootstrap\AbstractLaravelTestsBootstrapper<extended>
 */
class AbstractLaravelTestsBootstrapperTest extends AbstractTestCase
{
    /**
     * @throws \Exception
     */
    public function testBootstrapper(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageRegExp('~stub is works~');

        new class extends AbstractLaravelTestsBootstrapper {
            use CreatesApplicationTrait;

            protected function bootLog(): bool
            {
                $this->log();

                return true;
            }

            /**
             * This method must be called automatically.
             *
             * @throws \Exception
             */
            protected function bootFoo(): void
            {
                throw new \Exception('stub is works');
            }
        };
    }
}
