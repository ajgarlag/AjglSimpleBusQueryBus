<?php

/*
 * AJGL SimpleBus QueryBus Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\SimpleBus\Message\Bus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CatchReturnMessageBusMiddlewareDecorator implements CatchReturnMessageBusMiddleware
{
    private $innerMiddleware;

    public function __construct(MessageBusMiddleware $innerMiddleware)
    {
        $this->innerMiddleware = $innerMiddleware;
    }

    /**
     * @return MessageBusMiddleware
     */
    public function getInnerMiddleware()
    {
        return $this->innerMiddleware;
    }

    public function handle(object $message, callable $next, &$return = null): void
    {
        if ($this->innerMiddleware instanceof CatchReturnMessageBusMiddleware) {
            $this->innerMiddleware->handle($message, $next, $return);
        } else {
            $decoratedNext = $this->decorateCallable($next, $return);
            $this->innerMiddleware->handle($message, $decoratedNext);
        }
    }

    /**
     * @param mixed $return
     *
     * @return callable
     */
    private function decorateCallable(callable $next, &$return)
    {
        return function ($message) use ($next, &$return) {
            $return = $next($message);
        };
    }
}
