<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Client\Request;


use Nette\Utils\Json;
use Ofce\Netatmo\Client\Response\Response;
use Ofce\Netatmo\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;


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

	public function processResponse(ResponseInterface $response): Response
	{
		if ($response->getStatusCode() !== 200) {
			throw new RequestException('Returned http code ' . $response->getStatusCode());
		}

		$contents = Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);
		dump($contents);
		die();
	}
}