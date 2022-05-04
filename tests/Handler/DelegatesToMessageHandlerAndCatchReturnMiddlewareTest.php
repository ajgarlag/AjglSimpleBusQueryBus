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

use Ajgl\SimpleBus\Message\Handler\DelegatesToMessageHandlerAndCatchReturnMiddleware;
use PHPUnit\Framework\TestCase;

class DelegatesToMessageHandlerAndCatchReturnMiddlewareTest extends TestCase
{
    /**
     * @dataProvider provideMessages
     */
    public function testCatchReturnedValue($message)
    {
        $middleware = new DelegatesToMessageHandlerAndCatchReturnMiddleware(new ReturnMessageCallableResolver());
        $result = $middleware->handle($message, function () {return;}, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    public function provideMessages()
    {
        return array(
            array(new \stdClass()),
        );
    }
}
