<?php

namespace Test\Functional;

use PHPUnit\Framework\TestCase;

use FireLog\FireLog;
use FireLog\Handlers\BaseHandler;
use FireLog\Util\LogLevelEnum;

class LoggingTest extends TestCase
{
    public function tearDown(): void {
        \Mockery::close();
    }

    public function testConcurrency()
    {
        $conf = new Class {
            public array $handlers = [];
            public LogLevelEnum $maxLevel = LogLevelEnum::Debug;
        };

        /*
         * Each handler takes 3s to finish its write(), so this would be 21s of blocking execution without
         * the EventLoop.
         */
        for ($i = 0; $i < 7; $i++) {
            $conf->handlers['debug'][] = $this->getMockHandler();
        }

        $fLog = new FireLog($conf);

        $start = microtime(true);
        $fLog->debug('debug');
        $this->assertLessThan(4.0, microtime(true) - $start); // did we finish in under 4s?
    }

    private function getMockHandler(): object
    {
        return new Class() extends BaseHandler
        {
            protected const DELAY_SECS = 3;
        };
    }
}
