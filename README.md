# FireLog

Non-blocking async logging, using vanilla PHP 8.1 Fibers. PSR-3 compliant. Mostly.

## Install and Run

System has PHP >= 8.1, and composer:

```
$ composer dump-autoload
$ php example.php
```

System has dockerd:

```
$ docker run --rm --interactive --tty --volume $PWD:/app composer dump-autoload
$ docker run -it --rm -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:8.2.4-cli php example.php
```

## Docco

```php
include_once (__DIR__ . '/vendor/autoload.php');

use \FireLog\FireLog;
use \FireLog\Util\LogLevelEnum;

$fLog = new FireLog(new Config());

//PSR-3 interface
$fLog->log(LogLevelEnum::Debug, 'Hello World');
$fLog->warning('a warning');
$fLog->error('an error');
```

Config object should expose an array of classnames per log level called`$handlers`, and a LogLevelEnum `$maxLevel` to configure which handlers are used for each PSR-3 level.

```php
class Config
{
	public array $array = [
		'debug' => [\FireLog\Handlers\ConsoleHandler::class],
		'notice' => [], // etc. etc.
	];

	public LogLevelEnum $maxLevel = LogLevelEnum::Debug;
}
```

Handlers must implement a `write()` method or extend `BaseHandler`. Handlers will only be non-blocking if they return a new `\Fiber` instance that manages its own suspension: they must check for a condition and call `\Fiber::suspend` if not ready to return. The EventLoop will resume suspended handler fibres automatically. See `BaseHandler::write()` for the basic technique.