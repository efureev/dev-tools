<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Laravel\VarDumper;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class VarDumperMiddleware
 * @package AvtoDev\DevTools\Laravel\VarDumper
 * @link https://laravel.com/docs/5.5/middleware#defining-middleware Before & After Middleware
 */
class VarDumperMiddleware
{
    /**
     * @var DumpStackInterface
     */
    protected $stack;

    /**
     * Middleware constructor.
     *
     * @param DumpStackInterface $stack
     */
    public function __construct(DumpStackInterface $stack)
    {
        $this->stack = $stack;
    }

    /**
     * Modify response after the request is handled by the application.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($this->stack->count() > 0) {
            $dumped = \implode(\PHP_EOL, $this->stack->all());

            $this->stack->clear();

            $response->setContent($dumped . $response->getContent());
        }

        return $response;
    }
}
