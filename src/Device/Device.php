<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Device;

abstract class Device
{
	protected string $name;

	protected string $macAddress;

	/**
	 * @return string[]
	 */
	abstract public static function getOauthScopes(): array;

	public function __construct(string $name, string $macAddress)
	{
		$this->name = $name;
		$this->macAddress = $macAddress;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getMacAddress(): string
	{
		return $this->macAddress;
	}

	abstract public function getDeviceType(): string;
}
