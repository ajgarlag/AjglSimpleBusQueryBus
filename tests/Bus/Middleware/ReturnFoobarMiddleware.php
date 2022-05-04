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

use Ajgl\SimpleBus\Message\Bus\Middleware\CatchReturnMessageBusMiddleware;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class ReturnFoobarMiddleware implements CatchReturnMessageBusMiddleware
{
    public function handle(object $message, callable $next, &$return = null): void
    {
        $next($message);
        $return = 'foobar';
    }
}
