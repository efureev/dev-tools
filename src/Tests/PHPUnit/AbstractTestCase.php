<?php

declare(strict_types = 1);

namespace AvtoDev\DevTools\Tests\PHPUnit;

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTestCase
 * @package AvtoDev\DevTools\Tests\PHPUnit
 */
abstract class AbstractTestCase extends TestCase
{
    use Traits\AdditionalAssertionsTrait,
        Traits\InstancesAccessorsTrait,
        Traits\CarbonAssertionsTrait;
}
