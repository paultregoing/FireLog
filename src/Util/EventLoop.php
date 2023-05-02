<?php

namespace FireLog\Util;

class EventLoop
{
    /**
     * @var array Holds all of the non-terminated fibers. Static to make it available across all Fiber stack frames.
     */
    private static array $awaiting = [];

    /**
     * Add parent and child fibers to the container, and start the child.
     * @param \Fiber $child
     * @return mixed
     * @throws \Throwable
     */
    public static function await(\Fiber $child): mixed
    {
        self::$awaiting[] = [\Fiber::getCurrent(), $child];
        $child->start();

        /*
         * Loop on the child fiber's status. Child implementation is responsible for suspending itself if
         * not yet finished, otherwise it will block.
         */
        while (!$child->isTerminated()) {
            $child->resume();

            if (!$child->isTerminated()) {
                \Fiber::suspend();
            } else {
                break;
            }
        }

        return $child->getReturn();
    }

    /**
     * @return void
     */
    public static function run(): void
    {
        /*
         * The counterpart loop to the one in self::await()
         * Together they oscillate between resumed and suspended states for each parent fiber.
         * This way multiple fibers each get a chance to execute for a short time until the child
         * state flips to terminated (e.g. the network socket has EOF bytes, something timed out, or whatever)
         */
        while (count(self::$awaiting) > 0) {
            $done = [];

            foreach (self::$awaiting as $index => $fibrePair) {
                $parent = $fibrePair[0];

                if ($parent->isSuspended() && !$parent->isTerminated()) {
                    $parent->resume();
                } elseif ($parent->isTerminated()) {
                    $done[] = $index;
                }
            }

            foreach ($done as $index) {
                unset(self::$awaiting[$index]);
            }
        }
    }
}