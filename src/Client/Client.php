<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Client;

use GuzzleHttp\Client as GuzzleClient;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Client\Request\Request;
use Ofce\Netatmo\Client\Response\AuthorizationResponse;
use Ofce\Netatmo\Client\Response\Response;


final class Client
{
	public const GRANT_PASSWORD = 'password';

	/** @var string */
	private $apiUrl;

	/** @var GuzzleClient */
	private $client;

	/** @var AuthorizationRequest */
	private $authorizationRequest;

	/** @var string|null */
	private $accessToken = null;

	public function __construct(string $apiUrl, AuthorizationRequest $authorizationRequest)
	{
		$this->apiUrl = $apiUrl;
		$this->authorizationRequest = $authorizationRequest;
		$this->client = new GuzzleClient(['base_uri' => $apiUrl]);
	}

	private function sendAuthorizationRequest(): AuthorizationResponse
	{
		/** @var AuthorizationResponse $response */
		$response = $this->sendRequest($this->authorizationRequest);
		return $response;
	}

	public function getAccessToken(): string
	{
		if ($this->accessToken !== null) {
			return $this->accessToken;
		}

		//TODO CACHING OF ACCESS TOKEN
		$authorizationResponse = $this->sendAuthorizationRequest();

		$this->accessToken = $authorizationResponse->getAccessToken();
		return $this->accessToken;
	}

	public function sendRequest(Request $request): Response
	{
		$options = [];
		if ($request->hasBody()) {
			$options = [
				'form_params' => $request->getBody()
			];
		}

		return $request->processResponse($this->client->send($request->getHttpRequest(), $options));
	}
}