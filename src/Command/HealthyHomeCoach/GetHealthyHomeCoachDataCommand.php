<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Command\HealthyHomeCoach;

use Ofce\Netatmo\Exception\UnknownDeviceException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetHealthyHomeCoachDataCommand extends BaseHealthyHomeCoachCommand
{
	public function configure(): void
	{
		$this->setName('healthyHomeCoach:getData');
		$this->setDescription('Sends request to device and display data from Healthy home coach');
		$this->addArgument('device', InputArgument::REQUIRED, 'Enter Healthy home coach device name');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var string $deviceName */
		$deviceName = $input->getArgument('device');

		$console = new SymfonyStyle($input, $output);

		$console->title('<info>Healthy home coach data request</info>');

		$console->section('<info>Sending request</info>');

		try {
			$response = $this->healthyHomeCoachFacade->getHealthyHomeCoachData($deviceName);
		} catch (UnknownDeviceException $e) {
			$console->text(sprintf('<error>%s</error>', $e->getMessage()));
			$this->logger->addException($e);
			return 2;
		}

		$console->text('Request sended successfully');

		$console->section('<info>Displaying info</info>');

		$data = $response->getHealthyHomeCoachData();

		$console->table(
			['Type', 'Value'],
			$data->getConsoleOutput()
		);

		$this->logger->addDebug('Get healthy home coach data request finished successfully', $data->getConsoleOutput());

		return 0;
	}
}
