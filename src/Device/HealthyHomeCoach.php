<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Device;

class HealthyHomeCoach extends Device
{
	public const OAUTH_SCOPE = 'read_homecoach';

	public const DEVICE_NAME = 'healthyHomeCoach';

	/**
	 * @return string[]
	 */
	public static function getOauthScopes(): array
	{
		return [self::OAUTH_SCOPE];
	}

	public function __construct(string $name, string $macAddress)
	{
		parent::__construct($name, $macAddress);
	}

	public function getDeviceType(): string
	{
		return self::DEVICE_NAME;
	}
}
