<?php

/*
 * AJGL SimpleBus QueryBus Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\SimpleBus\Message\Handler;

use Ajgl\SimpleBus\Message\Bus\Middleware\CatchReturnMessageBusMiddleware;
use SimpleBus\Message\Handler\Resolver\MessageHandlerResolver;

class DelegatesToMessageHandlerAndCatchReturnMiddleware implements CatchReturnMessageBusMiddleware
{
    /**
     * @var MessageHandlerResolver
     */
    private $messageHandlerResolver;

    public function __construct(MessageHandlerResolver $messageHandlerResolver)
    {
        $this->messageHandlerResolver = $messageHandlerResolver;
    }

    /**
     * Handles the message by resolving the correct message handler and calling it.
     *
     * {@inheritdoc}
     */
    public function handle(object $message, callable $next, &$return = null): void
    {
        $handler = $this->messageHandlerResolver->resolve($message);
        $return = call_user_func($handler, $message);

        $next($message);
    }
}
