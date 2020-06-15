<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Facade;

use Ofce\Netatmo\Client\Request\HealthyHomeCoachDataRequest;
use Ofce\Netatmo\Client\Response\HealthyHomeCoachResponse;
use Ofce\Netatmo\Configuration\Configuration;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Exception\RequestException;
use Ofce\Netatmo\Exception\UnknownDeviceException;

class HealthyHomeCoachFacade
{
	private Configuration $configuration;

	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * @throws UnknownDeviceException
	 * @throws RequestException
	 */
	public function getHealthyHomeCoachData(string $deviceName): HealthyHomeCoachResponse
	{
		/** @var HealthyHomeCoach $device */
		$device = $this->configuration->getDeviceByName($deviceName, HealthyHomeCoach::DEVICE_NAME);

		$request = new HealthyHomeCoachDataRequest(
			$this->configuration->getClient()->getAccessToken(),
			$device->getMacAddress()
		);

		/** @var HealthyHomeCoachResponse $response */
		$response = $this->configuration->getClient()->sendRequest($request);

		return $response;
	}
}
