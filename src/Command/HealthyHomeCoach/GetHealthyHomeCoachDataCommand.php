<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Command\HealthyHomeCoach;


use Ofce\Netatmo\Client\Request\HealthyHomeCoachDataRequest;
use Ofce\Netatmo\Client\Response\HealthyHomeCoachResponse;
use Ofce\Netatmo\Command\BaseCommand;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Exception\UnknownDeviceException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class GetHealthyHomeCoachDataCommand extends BaseCommand
{
	public function configure(): void
	{
		$this->setName('healthyHomeCoach:getData');
		$this->setDescription('Sends request to device and display data from Healthy home coach');
		$this->addArgument('device', InputArgument::REQUIRED, 'Enter Healthy home coach device name');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var string $deviceName */
		$deviceName = $input->getArgument('device');

		$console = new SymfonyStyle($input, $output);

		$console->title('<info>Healthy home coach data request</info>');

		try {
			/** @var HealthyHomeCoach $device */
			$device = $this->configuration->getDeviceByName($deviceName, HealthyHomeCoach::DEVICE_NAME);
		} catch (UnknownDeviceException $e) {
			$console->text(sprintf('<error>%s</error>', $e->getMessage()));
			exit(2);
		}

		$console->section('<info>Sending request</info>');

		$request = new HealthyHomeCoachDataRequest(
			$this->configuration->getClient()->getAccessToken(),
			$device->getMacAddress()
		);

		/** @var HealthyHomeCoachResponse $response */
		$response = $this->configuration->getClient()->sendRequest($request);

		$console->text('Request sended successfully');

		$console->section('<info>Displaying info</info>');

		$data = $response->getHealthyHomeCoachData();

		$console->table(
			['Type', 'Value'],
			$data->getConsoleOutput()
		);

		exit(1);
	}
}