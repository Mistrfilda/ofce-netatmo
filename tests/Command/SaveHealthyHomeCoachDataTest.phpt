<?php

declare(strict_types=1);

use Ofce\Netatmo\Command\HealthyHomeCoach\GetHealthyHomeCoachDataCommand;
use Ofce\Netatmo\Command\HealthyHomeCoach\SaveHealthyHomeCoachDataCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;


require __DIR__ . '/BaseCommandHealthyHomeCoachTest.php';
require __DIR__ . '/../Bootstrap.php';

$healthyHomeCoachCommand = new SaveHealthyHomeCoachDataCommand($mockedConfiguration);

$commandTester = new CommandTester($healthyHomeCoachCommand);

$commandTester->execute([
	'device' => 'device1',
	'file' => __DIR__ .'/test.txt'
]);

$output = $commandTester->getDisplay();


Assert::contains('Sending request', $output);
Assert::contains('Request sended successfully', $output);
Assert::contains('Saving data into file', $output);
Assert::contains('Data successfully saved', $output);

$commandTester->execute([
	'device' => 'device1',
	'onlyTemperature' => 1,
	'file' => __DIR__ .'/wwwwwww/test.txt'
]);

$commandTester->getDisplay();

Assert::equal(2, $commandTester->getStatusCode());

$commandTester->execute([
	'device' => 'device123',
	'file' => __DIR__ .'/test.txt'
]);

Assert::equal(2, $commandTester->getStatusCode());