<?php


declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(['Naith.local', '172.16.15.2']);

$configurator->enableTracy(__DIR__ . '/../var/log');
$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../var');

$configurator->addConfig(__DIR__ . '/../etc/config.neon');
$configurator->addConfig(__DIR__ . '/../etc/config.local.neon');

return $configurator->createContainer();
