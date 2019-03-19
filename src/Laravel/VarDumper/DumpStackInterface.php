<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Laravel\VarDumper;

use Countable;

/**
 * Interface DumpStackInterface
 * @package AvtoDev\DevTools\Laravel\VarDumper
 */
interface DumpStackInterface extends Countable
{
    /**
     * Push an element into stack.
     *
     * @param string $data
     */
    public function push(string $data): void;

    /**
     * Clear stack.
     */
    public function clear(): void;

    /**
     * Get all stack elements.
     *
     * @return string[]
     */
    public function all(): array;
}
