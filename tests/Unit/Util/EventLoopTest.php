<?php

namespace Test\Functional;

use PHPUnit\Framework\TestCase;

use FireLog\Util\EventLoop;

class EventLoopTest extends TestCase
{
    public function testQueuingAndProcessingEvents()
    {
        $main = new \Fiber(function () {
            for ($i = 0; $i < 3; $i++) {
                $parent = new \Fiber(function () {
                    EventLoop::await(new \Fiber(fn() => true)); // will return instantly
                });
                $parent->start();
            }
        });
        $main->start();

        $this->assertEquals(3, EventLoop::countAwaiting());
        EventLoop::process();
        $this->assertEquals(0, EventLoop::countAwaiting());
    }
}
