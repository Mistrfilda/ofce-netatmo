<?php

declare(strict_types=1);

use Ofce\Netatmo\Command\HealthyHomeCoach\GetHealthyHomeCoachDataCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;


require __DIR__ . '/BaseCommandHealthyHomeCoachTest.php';
require __DIR__ . '/../Bootstrap.php';

$healthyHomeCoachCommand = new GetHealthyHomeCoachDataCommand($mockedConfiguration);

$commandTester = new CommandTester($healthyHomeCoachCommand);

$commandTester->execute([
	'device' => 'device1'
]);

$output = $commandTester->getDisplay();

Assert::contains('2019-06-10 21:21:39', $output);
Assert::contains('Temperature', $output);
Assert::contains('Absolute pressure', $output);
Assert::contains('24.3 °C at 2019-06-10 04:14:47', $output);
Assert::contains('29 °C at 2019-06-10 16:18:51', $output);
Assert::contains('Healthy home coach data request', $output);