<?php

use Test\Unit\FireLog\FireLog as FireLogTest;

include_once __DIR__ . "/../vendor/autoload.php";

echo $baseDir;
echo $vendorDir;
ob_flush();
$foo = new FireLogTest();