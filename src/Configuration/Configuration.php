<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Configuration;

use GuzzleHttp\Client as GuzzleClient;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Ofce\Netatmo\Client\Client;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Device\Device;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Exception\ConfigurationException;
use Ofce\Netatmo\Exception\UnknownDeviceException;
use Ofce\Netatmo\Logger\Logger;

final class Configuration
{
	public const CONFIG_LOCATION = __DIR__ . '/config.local.neon';

	/** @var Client */
	private $client;

	/** @var Device[] */
	private $devices = [];

	/** @var Logger */
	private $logger;

	/** @var Cache */
	private $cache;

	public function __construct(?string $configFile = null)
	{
		if ($configFile === null) {
			$configFile = self::CONFIG_LOCATION;
		}

		$neonConfig = @file_get_contents($configFile);
		if ($neonConfig === false) {
			throw new ConfigurationException('Missing configuration file');
		}

		$parameters = Neon::decode($neonConfig);
		$this->validateConfig($parameters);

		//Process healthy home coach, just for now, maybe more devices in the future :)
		foreach ($parameters['devices']['healthyHomeCoach'] as $name => $healthyHomeCoach) {
			//Useless for now, again for future use :)
			if (array_key_exists($name, $this->devices)) {
				throw new ConfigurationException(
					sprintf('Duplicate device name %s, devices must be unique across all types', $name)
				);
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

		$this->cache = new Cache(new FileStorage(__DIR__ . '/../../temp'));

		$guzzleClient = new GuzzleClient(['base_uri' => $parameters['netatmoApi']['baseUrl']]);

		$this->logger = new Logger($parameters['logger']['name'], $parameters['logger']['handlers']);
		$this->client = new Client($guzzleClient, $authorizationRequest, $this->cache, $this->logger);
	}

	public function getClient(): Client
	{
		return $this->client;
	}

	public function getLogger(): Logger
	{
		return $this->logger;
	}

	public function getCache(): Cache
	{
		return $this->cache;
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

	/**
	 * @param mixed[] $parameters
	 */
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
				'password' => Expect::string()->required()->min(1.0),
			]),
			'devices' => Expect::structure([
				'healthyHomeCoach' => Expect::array()->min(1.0)->items(Expect::structure([
					'macAddress' => Expect::string()->required(),
				])),
			]),
			'logger' => Expect::structure([
				'name' => Expect::string()->required(),
				'handlers' => Expect::arrayOf(Entity::class)->min(1.0),
			]),
		]);

		$processor->process($schema, $parameters);
	}
}
