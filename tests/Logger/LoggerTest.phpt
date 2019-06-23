<?php

declare(strict_types=1);

require __DIR__ . '/../Bootstrap.php';

use Nette\Neon\Neon;
use Ofce\Netatmo\Logger\Logger;
use Tester\Assert;


$config = Neon::decode(file_get_contents(__DIR__ . '/logger.config.neon'));

$logger = new Logger($config['logger']['name'], $config['logger']['handlers']);

Assert::noError(function () use ($logger) {
	$logger->addDebug('test log', ['test' => 'abc']);
	$logger->addInfo('test log', ['test' => 'abc']);
	$logger->addNotice('test log', ['test' => 'abc']);
	$logger->addWarning('test log', ['test' => 'abc']);
	$logger->addCritical('test log', ['test' => 'abc']);
	$logger->addEmergency('test log', ['test' => 'abc']);
	$logger->addException(new InvalidArgumentException(), ['test' => 'abc']);
});

Assert::exception(function () {
	$config = Neon::decode(file_get_contents(__DIR__ . '/wrong.config.neon'));
	new Logger($config['logger']['name'], $config['logger']['handlers']);
}, InvalidArgumentException::class, 'Invalid handler specified for monolog');