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

use Ajgl\SimpleBus\Message\Bus\Middleware\CatchReturnMessageBusSupportingMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CatchReturnMessageBusSupportingMiddlewareTest extends TestCase
{
    private $bus;

    protected function setUp(): void
    {
        $this->bus = new CatchReturnMessageBusSupportingMiddleware(
            [
                new PipeMiddleware(),
                new PipeMiddleware(),
                new PipeCatchReturnMiddleware(),
                new PipeMiddleware(),
                new PipeMiddleware(),
                new ReturnMessageMiddleware(),
            ]
        );
    }

    /**
     * @dataProvider provideMessages
     */
    public function testIngnoringReturnValueFromMiddlewaresInConstructor($message)
    {
        $result = $this->bus->handle($message);
        $this->assertNull($result);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testReturnValueFromMiddlewaresInConstructor($message)
    {
        $return = null;
        $result = $this->bus->handle($message, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testReturnValueWithPrependedMiddleware($message)
    {
        $this->bus->prependMiddleware(new PipeCatchReturnMiddleware());
        $this->bus->prependMiddleware(new PipeMiddleware());
        $this->bus->prependMiddleware(new PipeCatchReturnMiddleware());
        $return = null;
        $result = $this->bus->handle($message, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testOverwriteReturnValueFromPrependedMiddlewares($message)
    {
        $return = null;

        $result = $this->bus->handle($message, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);

        $this->bus->prependMiddleware(new ReturnFoobarMiddleware());
        $result = $this->bus->handle($message, $return);
        $this->assertNull($result);
        $this->assertSame('foobar', $return);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testAppendedMiddlewaresDoNotOverwriteReturnValue($message)
    {
        $return = null;

        $result = $this->bus->handle($message, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);

        $this->bus->appendMiddleware(new ReturnFoobarMiddleware());
        $result = $this->bus->handle($message, $return);
        $this->assertNull($result);
        $this->assertSame($message, $return);
    }

    public function provideMessages()
    {
        return [
            [new \stdClass()],
        ];
    }
}
