# FireLog

Non-blocking async logging, using vanilla PHP 8.1

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

