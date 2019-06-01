<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Configuration;


use Ofce\Netatmo\Exception\ConfigurationException;
use Nette\Neon\Neon;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Ofce\Netatmo\Client\Client;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Device\Device;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Exception\UnknownDeviceException;


final class Configuration
{
	public const CONFIG_LOCATION = __DIR__ . '/config.local.neon';

	/** @var Client */
	private $client;

	/** @var Device[] */
	private $devices = [];

	public function __construct(?string $configFile = null)
	{
		if ($configFile === null) {
			$configFile = self::CONFIG_LOCATION;
		}

		$neonConfig = @file_get_contents($configFile);
		if ($neonConfig === false) {
			throw new ConfigurationException('Missing config file src/Configuration/config.local.neon');
		}

		$parameters = Neon::decode($neonConfig);

		$this->validateConfig($parameters);

		//Process healthy home coach, just for now, maybe more devices in the future :)
		foreach ($parameters['devices']['healthyHomeCoach'] as $name => $healthyHomeCoach) {
			//Useless for now, again for future use :)
			if (array_key_exists($name, $this->devices)) {
				throw new ConfigurationException('Duplicate device name, devices must be unique across all types');
			}

			$this->devices[$name] = new HealthyHomeCoach($name, $healthyHomeCoach['macAddress']);
		}

		$scopes = HealthyHomeCoach::getOauthScopes();

		$authorizationRequest = new AuthorizationRequest(
			$parameters['credentials']['clientId'],
			$parameters['credentials']['clientSecret'],
			$parameters['credentials']['username'],
			$parameters['credentials']['password'],
			implode(' ', $scopes)
		);

		$this->client = new Client($parameters['netatmoApi']['baseUrl'], $authorizationRequest);
	}

	public function getClient(): Client
	{
		return $this->client;
	}

	/**
	 * @return Device[]
	 */
	public function getDevices(): array
	{
		return $this->devices;
	}

	/**
	 * @param string $name
	 * @param string|null $deviceType
	 * @return Device
	 * @throws UnknownDeviceException
	 */
	public function getDeviceByName(string $name, ?string $deviceType = null): Device
	{
		if (array_key_exists($name, $this->devices)) {
			if ($deviceType !== null && $this->devices[$name]->getDeviceType() !== $deviceType) {
				throw new UnknownDeviceException(
					sprintf('Specified device %s is listed under different device type', $name)
				);
			}

			return $this->devices[$name];
		}

		throw new UnknownDeviceException(sprintf('Missing device %s in configuration', $name));
	}

	private function validateConfig(array $parameters): void
	{
		$processor = new Processor();

		$schema = Expect::structure([
			'netatmoApi' => Expect::structure([
					'baseUrl' => Expect::string()->required(),
			]),
			'credentials' => Expect::structure([
				'clientId' => Expect::string()->required()->min(10.0),
				'clientSecret' => Expect::string()->required()->min(10.0),
				'username' => Expect::string()->required()->min(1.0),
				'password' => Expect::string()->required()->min(1.0)
			]),
			'devices' => Expect::structure([
				'healthyHomeCoach' => Expect::array()->min(1.0)->items(Expect::structure([
					'macAddress' => Expect::string()->required()
				]))
			])
		]);

		$processor->process($schema, $parameters);
	}
}