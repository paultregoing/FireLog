<?php

namespace FireLog;

use FireLog\Handlers\FakeDBHandler;
use FireLog\Handlers\FakeEmailHandler;
use FireLog\Handlers\ConsoleHandler;
use FireLog\Util\EventLoop;
use FireLog\Util\LoggerInterface;
use FireLog\Util\LogLevelEnum;

class FireLog implements LoggerInterface
{
    private object $conf;
    private LogLevelEnum $maxLevel;

    public function __construct(object $conf)
    {
        $this->conf = $conf;
        $this->maxLevel = $conf->maxLevel;
    }
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Debug;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Info;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Notice;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Warning;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Error;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Critical;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Alert;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $start = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' START' . PHP_EOL);

        $level = LogLevelEnum::Emergency;
        if (LogLevelEnum::requestedLevelAllowed($level, $this->maxLevel)) {
            $this->doLog($message, $this->conf->handlers[$level->value]);
        } else {
            fwrite(STDOUT, "NOT ALLOWED");
        }

        $end = microtime(true);
        fwrite(STDOUT, __METHOD__ . ' FINISHED ' . $end - $start . ' secs' . PHP_EOL);
    }

    public function log(LogLevelEnum $level, string|\Stringable $message, array $context = []): void
    {
        $method = $level->value;
        $this->$method($message);
    }

    private function doLog(string $message, array $handlers): void
    {
        $mainFiber = new \Fiber(function () use ($message, $handlers): void {
            /*
             * Let's say we have to log to both DB and Email for this channel.
             * We create two parent fibers, and call each handler's write() method.
             *
             * The handler::write() method returns a (child) fiber, which monitors its own state and suspends
             * itself to allow other fibers to execute.
             *
             * When the child is done it will return, then enter the terminated state.
             * Terminated fibers are removed from the loop, eventually the loop is empty and we're done.
             */
            foreach ($handlers as $handlerClass) {
                $parent = new \Fiber(function () use ($message, $handlerClass) {
                    $handler = new $handlerClass;
                    EventLoop::await($handler->write($message));
                });
                $parent->start();
            }

            EventLoop::run();
        });

        $mainFiber->start();
    }
}
