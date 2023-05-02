<?php

namespace FireLog\Handlers;

class ConsoleHandler implements HandlerInterface
{
    public function write(string $message): \Fiber
    {
        return new \Fiber(function () use ($message): void {
            $start = microtime(true);
            fwrite(STDOUT, static::class . ' starting' . PHP_EOL);

            // THIS IS A BLOCKING CALL, but it's fast so we'll give it a pass.
            fwrite(STDOUT, ' >>>LOGGED TO CONSOLE : ' . $message . PHP_EOL);

            $end = microtime(true);
            fwrite(STDOUT, static::class . ' done after ' . $end - $start . ' secs' . PHP_EOL);
        });
    }
}
