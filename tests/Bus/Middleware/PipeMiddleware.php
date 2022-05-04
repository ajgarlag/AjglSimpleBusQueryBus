<?php

/*
 * AJGL SimpleBus QueryBus Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\SimpleBus\Message\Tests\Bus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class PipeMiddleware implements MessageBusMiddleware
{
    public function handle(object $message, callable $next): void
    {
        $next($message);
    }
}
