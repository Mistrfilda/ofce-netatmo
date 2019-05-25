<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Device;


abstract class Device
{
	/** @var string */
	protected $name;

	/** @var string */
	protected $macAddress;

	public function __construct(string $name, string $macAddress)
	{
		$this->name = $name;
		$this->macAddress = $macAddress;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getMacAddress()
	{
		return $this->macAddress;
	}

	/** @return string[] */
	public abstract static function getOauthScopes(): array;
}