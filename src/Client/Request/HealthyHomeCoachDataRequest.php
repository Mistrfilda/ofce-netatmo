<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Client\Request;


final class HealthyHomeCoachDataRequest extends Request
{
	public function __construct(string $accessToken, string $deviceId)
	{
		parent::__construct(
			Request::METHOD_POST,
			'api/gethomecoachsdata',
			[
				'access_token' => $accessToken,
				'device_id' => $deviceId
			]
		);
	}
}