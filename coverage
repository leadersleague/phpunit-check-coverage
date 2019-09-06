<?php

use Coverage\Check;

require 'vendor/autoload.php';

if (!isset($argv[1])) {
    throw new InvalidArgumentException('No argument of path file provided');
}

if (!isset($argv[2])) {
    $argv[2] = 100;
}

try {
    echo sprintf('Run coverage check:'.PHP_EOL);
    $coverage = (new Check())->run($argv[1], $argv[2]);
    echo sprintf('%d percent', $coverage);
} catch (ErrorException $e) {
    echo 'Error: '.$e->getMessage();
    exit(1);
}
