<?php

declare(strict_types=1);

namespace Tests\AvtoDev\DevTools\Laravel\VarDumper;

use AvtoDev\DevTools\Laravel\VarDumper\DumpStack;
use Tests\AvtoDev\DevTools\AbstractTestCase;

/**
 * Class DumpStackTest
 * @package Tests\AvtoDev\DevTools\Laravel\VarDumper
 */
class DumpStackTest extends AbstractTestCase
{
    /**
     * @var DumpStack
     */
    protected $stack;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->stack = new DumpStack;
    }

    /**
     * @return void
     *
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack
     */
    public function testInterfaces(): void
    {
        static::assertInstanceOf(\Countable::class, $this->stack);
    }

    /**
     * @return void
     *
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::push
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::all
     *
     * @throws \Exception
     */
    public function testPush(): void
    {
        static::assertCount(0, $this->stack);

        $this->stack->push($value = 'foo_' . \random_int(1, 255));

        static::assertCount(1, $this->stack);
        static::assertSame($value, $this->stack->all()[0]);
    }

    /**
     * @return void
     *
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::clear
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::count
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::push
     */
    public function testClearAndCount(): void
    {
        $this->stack->push('foo');

        static::assertEquals(1, $this->stack->count());

        $this->stack->clear();

        static::assertEquals(0, $this->stack->count());
    }

    /**
     * @return void
     *
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::all
     * @covers \AvtoDev\DevTools\Laravel\VarDumper\DumpStack::push
     */
    public function testAll(): void
    {
        $data = ['baz', 'foo', 'bar'];

        foreach ($data as $item) {
            $this->stack->push($item);
        }

        static::assertSame($data, $this->stack->all());
    }
}
