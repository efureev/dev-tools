<?php

namespace Tests\AvtoDev\DevTools\Tests\Bootstrap;

use AvtoDev\DevTools\Tests\Bootstrap\AbstractTestsBootstrapper;
use Exception;
use Tests\AvtoDev\DevTools\AbstractTestCase;

/**
 * Class AbstractTestsBootstrapperTest
 * @package Tests\AvtoDev\DevTools\Tests\Bootstrap
 */
class AbstractTestsBootstrapperTest extends AbstractTestCase
{
    /**
     * @throws \Exception
     */
    public function testBootstrapper(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageRegExp('~stub is works~');

        new class extends AbstractTestsBootstrapper
        {
            /**
             * This method must be called automatically.
             *
             * @throws \Exception
             */
            protected function bootFoo(): void
            {
                throw new Exception('stub is works');
            }
        };
    }
}
