<?php

/*
 * AJGL SimpleBus QueryBus Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\SimpleBus\Message\Tests\Handler;

use SimpleBus\Message\Handler\Resolver\MessageHandlerResolver;

class ReturnMessageCallableResolver implements MessageHandlerResolver
{
    public function resolve($message)
    {
        return function ($message) { return $message; };
    }
}
