<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Device\Data;

use DateTimeImmutable;
use Ofce\Netatmo\Lib\DatetimeFactory;

class HealthyHomeCoachData
{
	/** @var DateTimeImmutable */
	private $time;

	/** @var float */
	private $temperature;

	/** @var float */
	private $CO2;

	/** @var float */
	private $humidity;

	/** @var float */
	private $noise;

	/** @var float */
	private $pressure;

	/** @var float */
	private $absolutePressure;

	/** @var int */
	private $healthIdx;

	/** @var float */
	private $minTemp;

	/** @var DateTimeImmutable */
	private $dateMinTemp;

	/** @var float */
	private $maxTemp;

	/** @var DateTimeImmutable */
	private $dateMaxTemp;

	public function __construct(
		int $time,
		float $temperature,
		float $CO2,
		float $humidity,
		float $noise,
		float $pressure,
		float $absolutePressure,
		int $healthIdx,
		float $minTemp,
		int $dateMinTemp,
		float $maxTemp,
		int $dateMaxTemp
	) {
		$this->time = new DateTimeImmutable('@' . $time);
		$this->temperature = $temperature;
		$this->CO2 = $CO2;
		$this->humidity = $humidity;
		$this->noise = $noise;
		$this->pressure = $pressure;
		$this->absolutePressure = $absolutePressure;
		$this->healthIdx = $healthIdx;
		$this->minTemp = $minTemp;
		$this->dateMinTemp = new DateTimeImmutable('@' . $dateMinTemp);
		$this->maxTemp = $maxTemp;
		$this->dateMaxTemp = new DateTimeImmutable('@' . $dateMaxTemp);
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getTime(): DateTimeImmutable
	{
		return $this->time;
	}

	/**
	 * @return float
	 */
	public function getTemperature(): float
	{
		return $this->temperature;
	}

	/**
	 * @return float
	 */
	public function getCO2(): float
	{
		return $this->CO2;
	}

	/**
	 * @return float
	 */
	public function getHumidity(): float
	{
		return $this->humidity;
	}

	/**
	 * @return float
	 */
	public function getNoise(): float
	{
		return $this->noise;
	}

	/**
	 * @return float
	 */
	public function getPressure(): float
	{
		return $this->pressure;
	}

	/**
	 * @return float
	 */
	public function getAbsolutePressure(): float
	{
		return $this->absolutePressure;
	}

	/**
	 * @return int
	 */
	public function getHealthIdx(): int
	{
		return $this->healthIdx;
	}

	/**
	 * @return float
	 */
	public function getMinTemp(): float
	{
		return $this->minTemp;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getDateMinTemp(): DateTimeImmutable
	{
		return $this->dateMinTemp;
	}

	/**
	 * @return float
	 */
	public function getMaxTemp(): float
	{
		return $this->maxTemp;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getDateMaxTemp(): DateTimeImmutable
	{
		return $this->dateMaxTemp;
	}

	/**
	 * @return mixed[]
	 */
	public function getConsoleOutput(): array
	{
		return [
			['Time', $this->time->format(DatetimeFactory::DATETIME_FORMAT)],
			['Temperature', sprintf('%s °C', $this->temperature)],
			['CO2', $this->CO2],
			['Humidity', $this->humidity],
			['Noise', $this->noise],
			['Pressure', $this->pressure],
			['Absolute pressure', $this->absolutePressure],
			['Health Idx', $this->healthIdx],
			['Min temp', sprintf('%s °C at %s', $this->minTemp, $this->dateMinTemp->format(DatetimeFactory::DATETIME_FORMAT))],
			['Max temp', sprintf('%s °C at %s', $this->maxTemp, $this->dateMaxTemp->format(DatetimeFactory::DATETIME_FORMAT))],
		];
	}
}
