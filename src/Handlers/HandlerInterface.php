<?php

namespace FireLog\Handlers;

interface HandlerInterface
{
    public function write(string $message): \Fiber;
}