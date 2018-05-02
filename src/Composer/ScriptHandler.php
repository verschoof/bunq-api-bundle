<?php

namespace Verschoof\BunqApiBundle\Composer;

use Composer\Script\CommandEvent;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

final class ScriptHandler
{
    public static function setup(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $appDir  = $options['symfony-app-dir'];

        self::runSetup($appDir);
    }

    public static function setupWithoutEvent($appDir, $force = false)
    {
        self::runSetup($appDir, $force);
    }

    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge([
            'symfony-app-dir'        => 'app',
            'symfony-web-dir'        => 'web',
            'symfony-assets-install' => 'hard',
        ], $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }

    private static function runSetup($appDir, $force = false)
    {
        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir (' . $appDir . ') specified in composer.json was not found in ' . getcwd() . ', can not clear the cache.' . PHP_EOL;

            return;
        }

        $php     = escapeshellarg(self::getPhp());
        $console = escapeshellarg($appDir . '/console');
        $cmd     = 'bunq:setup';
        if ($force) {
            $cmd = $cmd . ' --force';
        }

        $process = new Process($php . ' ' . $console . ' ' . $cmd, null, null, null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd))
            );
        }
    }
}
