<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Client\Request;


use Ofce\Netatmo\Client\Client;


final class AuthorizationRequest extends Request
{
	public function __construct(
		string $clientId,
		string $clientSecret,
		string $username,
		string $password,
		string $scope
	)
	{
		parent::__construct(
			Request::METHOD_POST,
			'oauth2/token',
			[
				'grant_type' => Client::GRANT_PASSWORD,
				'scope' => $scope,
				'client_id' => $clientId,
				'client_secret' => $clientSecret,
				'username' => $username,
				'password' => $password
			]
		);
	}
}