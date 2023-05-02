<?php

namespace FireLog\Handlers;

class BaseHandler implements HandlerInterface
{
    protected const DELAY_SECS = 0;

    public function write(string $message): \Fiber
    {
        return new \Fiber(function (): string {
            $start = microtime(true);
            fwrite(STDOUT, static::class . ' starting' . PHP_EOL);

            /*
             * Let's fake up some non-blocking latency. A real implementation might use something based on
             * a non-blocking stream_socket_client()
             */
            do {
                $elapsed = microtime(true) - $start;
                if ($elapsed < static::DELAY_SECS) {
                    \Fiber::suspend(); // We _must_ do this or the method will block the event loop!
                }
            } while ($elapsed < static::DELAY_SECS);

            $end = microtime(true);
            fwrite(STDOUT, static::class . ' done after ' . $end - $start . ' secs' . PHP_EOL);

            return 'done';
        });
    }
}
