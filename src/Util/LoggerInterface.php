<?php

namespace Firelog\Util;

use Firelog\Util\LogLevelEnum;

/**
 * A PSR-3 compliant logger interface.
 *
 * I was told "no libraries", this is essentially Psr\Log\LoggerInterface
 * e.g. from "$ composer install psr/log ^3.0"
 */
interface LoggerInterface
{
    public function debug(string|\Stringable $message, array $context = []): void;

    public function info(string|\Stringable $message, array $context = []): void;

    public function notice(string|\Stringable $message, array $context = []): void;

    public function warning(string|\Stringable $message, array $context = []): void;

    public function error(string|\Stringable $message, array $context = []): void;

    public function critical(string|\Stringable $message, array $context = []): void;

    public function alert(string|\Stringable $message, array $context = []): void;

    public function emergency(string|\Stringable $message, array $context = []): void;

    public function log(LogLevelEnum $level, string|\Stringable $message, array $context = []): void;
}