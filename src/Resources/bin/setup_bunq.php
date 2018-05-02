#!/usr/bin/env php
<?php

if (PHP_SAPI !== 'cli') {
    echo 'Warning: ' . __FILE__ . ' should be invoked via the CLI version of PHP, not the ' . PHP_SAPI . ' SAPI' . PHP_EOL;
}

$argv = $_SERVER['argv'];
$argv = convertInputArguments();

// allow the base path to be passed as the first argument, or default
if (isset($argv[1]) && $argv[1]) {
    $appDir = $argv[1];
} else {
    if (!$appDir = realpath(__DIR__ . '/../../../../../../app')) {
        exit('Looks like you don\'t have a standard layout.');
    }
}

$force = false;
if (isset($argv[2])) {
    $force = $argv[2];
}

require_once $appDir . '/autoload.php';

\Verschoof\BunqApiBundle\Composer\ScriptHandler::setupWithoutEvent($appDir, $force);


function convertInputArguments()
{
    $argv = $_SERVER['argv'];
    foreach ($argv as $key => $value) {
        if ($value === 'null') {
            $value = null;
        }

        if ($value === 'true') {
            $value = true;
        }

        if ($value === 'false') {
            $value = false;
        }

        $argv[$key] = $value;
    }

    return $argv;
}
