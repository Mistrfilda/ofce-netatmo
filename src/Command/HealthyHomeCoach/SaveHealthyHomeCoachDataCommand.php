<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Command\HealthyHomeCoach;

use Nette\Utils\Json;
use Ofce\Netatmo\Client\Request\HealthyHomeCoachDataRequest;
use Ofce\Netatmo\Client\Response\HealthyHomeCoachResponse;
use Ofce\Netatmo\Command\BaseCommand;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Exception\UnknownDeviceException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SaveHealthyHomeCoachDataCommand extends BaseCommand
{
	public function configure(): void
	{
		$this->setName('healthyHomeCoach:saveData');
		$this->setDescription('Sends request to device and save data from Healthy home coach to text file');
		$this->addArgument('device', InputArgument::REQUIRED, 'Enter Healthy home coach device name');
		$this->addArgument('file', InputArgument::REQUIRED, 'Enter Healthy file name');
		$this->addArgument('onlyTemperature', InputArgument::OPTIONAL, 'Save only temperature to file?', '0');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var string $deviceName */
		$deviceName = $input->getArgument('device');

		/** @var string $file */
		$file = $input->getArgument('file');

		/** @var bool $onlyTemperatire */
		$onlyTemperature = (bool) $input->getArgument('onlyTemperature');

		$console = new SymfonyStyle($input, $output);

		$console->title('<info>Healthy home coach data request</info>');

		try {
			/** @var HealthyHomeCoach $device */
			$device = $this->configuration->getDeviceByName($deviceName, HealthyHomeCoach::DEVICE_NAME);
		} catch (UnknownDeviceException $e) {
			$console->text(sprintf('<error>%s</error>', $e->getMessage()));
			$this->logger->addException($e);
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

		$console->section('<info>Saving data into file</info>');

		$data = $response->getHealthyHomeCoachData();

		if ($onlyTemperature) {
			$file = @file_put_contents($file, $data->getTemperature());
		} else {
			$file = @file_put_contents($file, Json::encode($data->getConsoleOutput()));
		}

		if ($file === false) {
			$console->error('There was an error saving data to file, please check permissions');
			$this->logger->addCritical('There was an error saving data to file, please check permissions');
			exit(2);
		}

		$console->section('<info>Data successfully saved</info>');

		$this->logger->addInfo('Save healthy home coach data finished successfully', $data->getConsoleOutput());

		return 0;
	}
}
