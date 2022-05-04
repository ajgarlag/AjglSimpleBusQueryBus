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

use Ajgl\SimpleBus\Message\Bus\Middleware\CatchReturnMessageBusMiddlewareDecorator;
use PHPUnit\Framework\TestCase;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CatchReturnMessageBusMiddlewareDecoratorTest extends TestCase
{
    public function testGetInnerMiddleware()
    {
        $innerMiddleware = new PipeMiddleware();
        $middleware = new CatchReturnMessageBusMiddlewareDecorator($innerMiddleware);
        $this->assertSame($innerMiddleware, $middleware->getInnerMiddleware());
    }

    /**
     * @dataProvider provideMessages
     */
    public function testWithoutReturnVariable($message)
    {
        $innerMiddleware = new PipeMiddleware();
        $middleware = new CatchReturnMessageBusMiddlewareDecorator($innerMiddleware);
        $result = $middleware->handle($message, function ($message) { return $message; });
        $this->assertNull($result);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testWithReturnVariable($message)
    {
        $innerMiddleware = new PipeMiddleware();
        $middleware = new CatchReturnMessageBusMiddlewareDecorator($innerMiddleware);
        $return = null;
        $result = $middleware->handle($message, function ($message) { return $message; }, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testDoubleDecoration($message)
    {
        $innerMiddleware = new PipeMiddleware();
        $firstMiddleware = new CatchReturnMessageBusMiddlewareDecorator($innerMiddleware);
        $secondMiddleware = new CatchReturnMessageBusMiddlewareDecorator($firstMiddleware);
        $return = null;
        $result = $secondMiddleware->handle($message, function ($message) { return $message; }, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testReturningMessageBusMiddlewareDecoration($message)
    {
        $innerMiddleware = new PipeCatchReturnMiddleware();
        $middleware = new CatchReturnMessageBusMiddlewareDecorator($innerMiddleware);
        $return = null;
        $result = $middleware->handle($message, function ($message) { return $message; }, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    public function provideMessages()
    {
        return array(
            array(new \stdClass()),
            array(new \stdClass('query')),
        );
    }
}
