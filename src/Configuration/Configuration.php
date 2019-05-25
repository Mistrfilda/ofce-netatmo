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


final class Configuration
{
	/** @var Client */
	private $client;

	/** @var Device[] */
	private $devices = [];

	public function __construct()
	{
		$neonConfig = @file_get_contents(__DIR__ . '/config.local.neon');
		if ($neonConfig === false) {
			throw new ConfigurationException('Missing config file src/Configuration/config.local.neon');
		}

		$parameters = Neon::decode($neonConfig);

		dump($parameters);
		$this->validateConfig($parameters);

		//Process healthy home coach, just for now, maybe more devices in the future :)
		foreach ($parameters['devices']['healthyHomeCoach'] as $name => $healthyHomeCoach) {
			$this->devices[$name] = new HealthyHomeCoach($name, $healthyHomeCoach['macAddress']);
		}

		$scopes = HealthyHomeCoach::getOauthScopes();

		$authorizationRequest = new AuthorizationRequest(
			$parameters['credentials']['clientId'],
			$parameters['credentials']['clientSecret'],
			$parameters['credentials']['username'],
			$parameters['credentials']['password'],
			implode(',', $scopes)
		);

		$this->client = new Client($parameters['netatmoApi']['baseUrl'], $authorizationRequest);

		dump($this->client);

		die();
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