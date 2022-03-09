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
interface CatchReturnMessageBusMiddleware extends MessageBusMiddleware
{
    /**
     * Will reference the return of the execution into the $return parameter.
     *
     * @param object     $message
     * @param callable   $next
     * @param mixed|null $return
     */
    public function handle(object $message, callable $next, &$return = null): void;
}
