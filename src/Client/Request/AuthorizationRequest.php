<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Client\Request;

use Nette\Utils\Json;
use Ofce\Netatmo\Client\Client;
use Ofce\Netatmo\Client\Response\AuthorizationResponse;
use Ofce\Netatmo\Client\Response\Response;
use Ofce\Netatmo\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

final class AuthorizationRequest extends Request
{
	public function __construct(
		string $clientId,
		string $clientSecret,
		string $username,
		string $password,
		string $scope
	) {
		parent::__construct(
			Request::METHOD_POST,
			'oauth2/token',
			[
				'grant_type' => Client::GRANT_PASSWORD,
				'scope' => $scope,
				'client_id' => $clientId,
				'client_secret' => $clientSecret,
				'username' => $username,
				'password' => $password,
			]
		);
	}

	public function processResponse(ResponseInterface $response): Response
	{
		if ($response->getStatusCode() !== 200) {
			throw new RequestException('Returned http code ' . $response->getStatusCode());
		}

		$contents = Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);

		return new AuthorizationResponse($contents);
	}
}
