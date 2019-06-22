<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use Nette\Caching\Cache;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Client\Request\Request;
use Ofce\Netatmo\Client\Response\AuthorizationResponse;
use Ofce\Netatmo\Client\Response\Response;
use Ofce\Netatmo\Logger\Logger;

final class Client
{
	public const GRANT_PASSWORD = 'password';

	public const FORBIDDEN_RESOURCE_CODE = 403;

	/** @var GuzzleClient */
	private $client;

	/** @var AuthorizationRequest */
	private $authorizationRequest;

	/** @var Cache */
	private $cache;

	/** @var Logger */
	private $logger;

	public function __construct(
		GuzzleClient $client,
		AuthorizationRequest $authorizationRequest,
		Cache $cache,
		Logger $logger
	) {
		$this->authorizationRequest = $authorizationRequest;
		$this->cache = $cache;
		$this->logger = $logger;
		$this->client = $client;
	}

	public function getAccessToken(bool $refresh = false): string
	{
		$accessToken = $this->cache->load('netatmo-authorization-code');

		if ($accessToken !== null && $refresh === false) {
			return $accessToken;
		}

		$authorizationResponse = $this->sendAuthorizationRequest();

		$accessToken = $authorizationResponse->getAccessToken();

		$this->cache->save('netatmo-authorization-code', $accessToken, [
			Cache::EXPIRE => '20 minutes',
		]);

		return $accessToken;
	}

	public function sendRequest(Request $request, bool $forbiddenResend = true): Response
	{
		$options = [];
		if ($request->hasBody()) {
			$options = [
				'form_params' => $request->getBody(),
			];
		}

		try {
			$response = $this->client->send($request->getHttpRequest(), $options);
		} catch (ClientException $e) {
			if ($e->getCode() === self::FORBIDDEN_RESOURCE_CODE && $forbiddenResend && $request->hasAccessToken()) {
				$this->logger->addNotice(
					'Resending request due to forbidden access, trying to refresh access token',
					['request' => get_class($request)]
				);

				$refreshAccessToken = $this->getAccessToken(true);
				$request->refreshAccessToken($refreshAccessToken);
				return $this->sendRequest($request, false);
			}

			throw $e;
		}

		return $request->processResponse($response);
	}

	private function sendAuthorizationRequest(): AuthorizationResponse
	{
		/** @var AuthorizationResponse $response */
		$response = $this->sendRequest($this->authorizationRequest);
		return $response;
	}
}
