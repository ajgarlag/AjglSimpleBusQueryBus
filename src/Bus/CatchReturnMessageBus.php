<?php

/*
 * AJGL SimpleBus QueryBus Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\SimpleBus\Message\Bus;

use SimpleBus\Message\Bus\MessageBus;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
interface CatchReturnMessageBus extends MessageBus
{
    /**
     * @param object     $message
     * @param mixed|null $return
     */
    public function handle($message, &$return = null);
}
