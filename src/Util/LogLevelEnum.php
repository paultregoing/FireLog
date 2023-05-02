<?php

namespace Firelog\Util;

/**
 * PSR-3 logger levels. Pretty similar to Monolog\Level.
 */
enum LogLevelEnum: string
{
    case Debug = 'debug';

    case Info = 'info';

    case Notice = 'notice';

    case Warning = 'warning';

    case Error = 'error';

    case Critical = 'critical';

    case Alert = 'alert';

    case Emergency = 'emergency';

    public static function requestedLevelAllowed(LogLevelEnum $requested, LogLevelEnum $max): bool
    {
        static $rfc_5424_levels = [
            self::Debug->value => LOG_DEBUG,
            self::Info->value => LOG_INFO,
            self::Notice->value => LOG_NOTICE,
            self::Warning->value => LOG_WARNING,
            self::Error->value => LOG_ERR,
            self::Critical->value => LOG_CRIT,
            self::Alert->value => LOG_ALERT,
            self::Emergency->value => LOG_EMERG,
        ];

        return ($rfc_5424_levels[$requested->value] <= $rfc_5424_levels[$max->value]);
    }
}