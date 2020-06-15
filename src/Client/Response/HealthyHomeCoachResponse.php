<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Client\Response;

use DateTimeImmutable;
use Exception;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Ofce\Netatmo\Device\Data\HealthyHomeCoachData;

class HealthyHomeCoachResponse extends Response
{
	private string $status;

	private float $executionTime;

	private DateTimeImmutable $deviceTime;

	private HealthyHomeCoachData $healthyHomeCoachData;

	public function getStatus(): string
	{
		return $this->status;
	}

	public function getExecutionTime(): float
	{
		return $this->executionTime;
	}

	public function getDeviceTime(): DateTimeImmutable
	{
		return $this->deviceTime;
	}

	public function getHealthyHomeCoachData(): HealthyHomeCoachData
	{
		return $this->healthyHomeCoachData;
	}

	/**
	 * @param mixed[] $response
	 * @throws Exception
	 */
	protected function createFromArrayResponse(array $response): void
	{
		$dashboardData = $response['body']['devices'][0]['dashboard_data'];
		$this->validateDashboardData($dashboardData);
		$this->status = $response['status'];
		$this->executionTime = $response['time_exec'];
		$this->deviceTime = new DateTimeImmutable('@' . $response['time_server']);
		$this->healthyHomeCoachData = new HealthyHomeCoachData(
			$dashboardData['time_utc'],
			$dashboardData['Temperature'],
			$dashboardData['CO2'],
			$dashboardData['Humidity'],
			$dashboardData['Noise'],
			$dashboardData['Pressure'],
			$dashboardData['AbsolutePressure'],
			$dashboardData['health_idx'],
			$dashboardData['min_temp'],
			$dashboardData['date_min_temp'],
			$dashboardData['max_temp'],
			$dashboardData['date_max_temp']
		);
	}

	protected function getResponseSchema(): Schema
	{
		return Expect::structure([
			'body' => Expect::structure([
				'devices' => Expect::array()->required()->min(1.0),
				'user' => Expect::array()->required(),
			])->required(),
			'status' => Expect::string()->required(),
			'time_exec' => Expect::float()->required(),
			'time_server' => Expect::int()->required(),
		]);
	}

	/**
	 * @param mixed[] $dashboardData
	 */
	private function validateDashboardData(array $dashboardData): void
	{
		$schema = Expect::structure([
			'time_utc' => Expect::int()->required()->min(1000.0),
			'Temperature' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'CO2' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'Humidity' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'Noise' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'Pressure' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'AbsolutePressure' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'health_idx' => Expect::int()->required(),
			'min_temp' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'max_temp' => Expect::anyOf(Expect::int(), Expect::float())->required(),
			'date_min_temp' => Expect::int()->required(),
			'date_max_temp' => Expect::int()->required(),
		]);

		(new Processor())->process($schema, $dashboardData);
	}
}
