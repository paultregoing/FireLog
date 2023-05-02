<?php

include_once (__DIR__ . '/vendor/autoload.php');

use \FireLog\FireLog;
use \FireLog\Handlers\ConsoleHandler;
use \FireLog\Handlers\FakeEmailHandler;
use \FireLog\Handlers\FakeDBHandler;
use \FireLog\Handlers\FakeSmsGatewayHandler;
use \FireLog\Util\LogLevelEnum;

$fLog = new FireLog(new Config());

fwrite(STDOUT, PHP_EOL . "First we'll log some debug messages to console..." . PHP_EOL);
$fLog->log(LogLevelEnum::Debug, 'test debug');
fwrite(STDOUT, PHP_EOL . PHP_EOL);

fwrite(STDOUT, "Now some Notice level messages" . PHP_EOL);
$fLog->notice('test notice');
fwrite(STDOUT, PHP_EOL . PHP_EOL);

fwrite(STDOUT, "Uh oh, we broke stuff. Now let's send an emergency, which has multiple slooooow handlers" . PHP_EOL);
$fLog->emergency("ALL OF THE THINGS ARE ON FIRE! THIS IS _NOT_ FINE.");
fwrite(STDOUT, PHP_EOL . PHP_EOL);

/**
 * For the sake of brevity we'll dump conf into a plain object. Ordinarily I'd be using a Yaml parser or similar.
 * Here we'll define the handler classes we want for each PSR-3 log level.
 */
class Config
{
    public array $handlers = [
        'debug' => [
            ConsoleHandler::class,
        ],
        'info' => [
            ConsoleHandler::class,
        ],
        'notice' => [
            ConsoleHandler::class,
            FakeDBHandler::class,
        ],
        'warning' => [
            ConsoleHandler::class,
            FakeDBHandler::class,
        ],
        'error' => [
            ConsoleHandler::class,
            FakeDBHandler::class,
        ],
        'critical' => [
            ConsoleHandler::class,
            FakeDBHandler::class,
        ],
        'alert' => [
            ConsoleHandler::class,
            FakeDBHandler::class,
        ],
        'emergency' => [
            ConsoleHandler::class,
            FakeDBHandler::class,
            FakeEmailHandler::class,
            FakeSmsGatewayHandler::class,
        ],
    ];

    public LogLevelEnum $maxLevel = LogLevelEnum::Debug;
}