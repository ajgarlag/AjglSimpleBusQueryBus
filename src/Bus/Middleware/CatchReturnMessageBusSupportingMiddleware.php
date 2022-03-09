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

use Ajgl\SimpleBus\Message\Bus\CatchReturnMessageBus;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CatchReturnMessageBusSupportingMiddleware extends MessageBusSupportingMiddleware implements CatchReturnMessageBus
{
    public function handle($message, &$return = null): void
    {
        $callable = $this->callableForNextMiddleware(0);
        $callable($message, $return);
    }

    private function callableForNextMiddleware(int $index): callable
    {
        $middlewares = $this->getMiddlewares();

        if (!isset($middlewares[$index])) {
            return static function () { return; };
        }

        $middleware = $middlewares[$index];
        $returningMiddleware = $this->decorateMiddlewareIfNeeded($middleware);

        return function ($message, &$return = null) use ($returningMiddleware, $index) {
            $returningMiddleware->handle($message, $this->callableForNextMiddleware($index + 1), $return);

            return $return;
        };
    }

    private function decorateMiddlewareIfNeeded(MessageBusMiddleware $middleware): MessageBusMiddleware
    {
        if (!$middleware instanceof CatchReturnMessageBusMiddleware) {
            $middleware = new CatchReturnMessageBusMiddlewareDecorator($middleware);
        }

        return $middleware;
    }
}
