<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Device\Data;

use DateTimeImmutable;
use Ofce\Netatmo\Lib\DatetimeFactory;

class HealthyHomeCoachData
{
	private DateTimeImmutable $time;

	private float $temperature;

	private float $CO2;

	private float $humidity;

	private float $noise;

	private float $pressure;

	private float $absolutePressure;

	private int $healthIdx;

	private float $minTemp;

	private DateTimeImmutable $dateMinTemp;

	private float $maxTemp;

	private DateTimeImmutable $dateMaxTemp;

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

	public function getTime(): DateTimeImmutable
	{
		return $this->time;
	}

	public function getTemperature(): float
	{
		return $this->temperature;
	}

	public function getCO2(): float
	{
		return $this->CO2;
	}

	public function getHumidity(): float
	{
		return $this->humidity;
	}

	public function getNoise(): float
	{
		return $this->noise;
	}

	public function getPressure(): float
	{
		return $this->pressure;
	}

	public function getAbsolutePressure(): float
	{
		return $this->absolutePressure;
	}

	public function getHealthIdx(): int
	{
		return $this->healthIdx;
	}

	public function getMinTemp(): float
	{
		return $this->minTemp;
	}

	public function getDateMinTemp(): DateTimeImmutable
	{
		return $this->dateMinTemp;
	}

	public function getMaxTemp(): float
	{
		return $this->maxTemp;
	}

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
