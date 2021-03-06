<?php

declare(strict_types=1);

namespace Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits;

use AvtoDev\DevTools\Tests\PHPUnit\Traits\AdditionalAssertionsTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\CreatesApplicationTrait;
use AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelRoutesAssertsTrait;
use Illuminate\Routing\Router;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\AvtoDev\DevTools\Tests\PHPUnit\Traits\Stubs\ControllerStub;

/**
 * @covers \AvtoDev\DevTools\Tests\PHPUnit\Traits\LaravelRoutesAssertsTrait<extended>
 */
class LaravelRoutesAssertsTraitTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplicationTrait,
        AdditionalAssertionsTrait,
        LaravelRoutesAssertsTrait;

    /**
     * @var Router
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Router::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        unset($this->router);
        parent::tearDown();
    }

    /**
     * Test assertion.
     *
     * @throws \InvalidArgumentException
     */
    public function testExistedRoute(): void
    {
        $this->router->get('example', ControllerStub::class . '@testAction');

        $this->assertAllRoutesHasActions();
    }

    /**
     * Test route with using method.
     *
     * @throws \InvalidArgumentException
     */
    public function testInvokedRoute(): void
    {
        $this->router->get('example', ControllerStub::class);

        $this->assertAllRoutesHasActions();
    }

    /**
     * Test non existing method in controller.
     *
     * @throws \InvalidArgumentException
     */
    public function testNotExistedMethod(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageRegExp('~Has no method named~');
        $this->router->get('example', ControllerStub::class . '@nonExistsAction');

        $this->assertAllRoutesHasActions();
    }

    /**
     * Test non existing controller class.
     *
     * @throws \InvalidArgumentException
     */
    public function testNotExistedClass(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageRegExp('~Class .* was not found~');

        $this->router->get('example', 'SomeClassThatNotExists@testAction');

        $this->assertAllRoutesHasActions();
    }
}
