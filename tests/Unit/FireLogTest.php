<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Hamcrest\Matchers as Matcher;

use FireLog\FireLog;
use FireLog\Util\LogLevelEnum;
use FireLog\Handlers\HandlerInterface;

class FireLogTest extends TestCase
{
    public function tearDown(): void {
        \Mockery::close();
    }

    public function testIsPsr3Compliant (): void
    {
        $fLog = new FireLog(new class {
            public LogLevelEnum $maxLevel = LogLevelEnum::Debug;
        });

        $this->assertTrue(method_exists($fLog, 'log'));
        $this->assertTrue(method_exists($fLog, 'debug'));
        $this->assertTrue(method_exists($fLog, 'info'));
        $this->assertTrue(method_exists($fLog, 'notice'));
        $this->assertTrue(method_exists($fLog, 'warning'));
        $this->assertTrue(method_exists($fLog, 'error'));
        $this->assertTrue(method_exists($fLog, 'alert'));
        $this->assertTrue(method_exists($fLog, 'emergency'));
        /*
         * we could go on from here to test interfaces of each method etc.
         */
    }

    /**
     * Separate process because we're aliasing with Mockery::mock()
     *
     * @runInSeparateProcess
     */
    public function testHandlerReceivesMessageAndCallsEventLoop(): void
    {
        $eventLoop = \Mockery::mock('alias:FireLog\Util\EventLoop');
        $eventLoop->shouldReceive('await')
            ->once()
            ->with(Matcher::anInstanceOf('Fiber'))
            ->andReturn(null);
        $eventLoop->shouldReceive('process')
            ->once();

        $mockHandler = $this->getMockHandler();

        $mockConf = new Class {
            public array $handlers = ['debug' => []];
            public LogLevelEnum $maxLevel = LogLevelEnum::Debug;
        };

        $mockConf->handlers['debug'][] = $mockHandler;
        $fLog = new FireLog($mockConf);

        $fLog->debug('testing');

        $this->assertEquals('testing', $mockHandler->getMsg());
    }

    /**
     * Separate process because we're aliasing with Mockery::mock()
     *
     * @runInSeparateProcess
     */
    public function testLogLevelPreventsCalls(): void
    {
        $eventLoop = \Mockery::mock('alias:FireLog\Util\EventLoop');
        $eventLoop->shouldReceive('await')
            ->once()
            ->with(Matcher::anInstanceOf('Fiber'))
            ->andReturn(null);
        $eventLoop->shouldReceive('process')
            ->once();

        $mockDebugHandler = $this->getMockHandler();
        $mockInfoHandler = $this->getMockHandler();

        $conf = new Class {
            public array $handlers = [
                'debug' => [],
                'info' => [],
            ];
            public LogLevelEnum $maxLevel = LogLevelEnum::Info;
        };

        $conf->handlers['debug'][] = $mockDebugHandler;
        $conf->handlers['info'][] = $mockInfoHandler;

        $fLog = new FireLog($conf);
        $fLog->debug('debug');
        $fLog->info('info');

        $this->assertEmpty($mockDebugHandler->getMsg());
        $this->assertEquals('info', $mockInfoHandler->getMsg());
    }

    private function getMockHandler(): object
    {
        return new Class() implements HandlerInterface
        {
            private string $msg = '';
            public function write(string $message): \Fiber
            {
                $this->msg = $message;
                return new \Fiber(fn () => true); // just to satisfy the interface requirements, doesn't need to suspend
            }

            public function getMsg(): string
            {
                return $this->msg;
            }
        };
    }
}
