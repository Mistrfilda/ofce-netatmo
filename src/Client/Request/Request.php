<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Client\Request;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Ofce\Netatmo\Client\Response\Response;
use Ofce\Netatmo\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

abstract class Request
{
	public const METHOD_GET = 'get';

	public const METHOD_POST = 'post';

	private GuzzleRequest $httpRequest;

	/**
	 * @var string[]
	 */
	private array $body;

	/**
	 * Request constructor.
	 * @param string[] $body
	 * @throws RequestException
	 */
	public function __construct(string $method, string $endpoint, array $body)
	{
		if (! in_array($method, [self::METHOD_GET, self::METHOD_POST], true)) {
			throw new RequestException('Unsupported METHOD');
		}

		$this->httpRequest = new GuzzleRequest(
			$method,
			$endpoint
		);

		$this->body = $body;
	}

	/**
	 * @return string[]
	 */
	public function getBody(): array
	{
		return $this->body;
	}

	public function getHttpRequest(): GuzzleRequest
	{
		return $this->httpRequest;
	}

	public function hasBody(): bool
	{
		return count($this->body) > 0;
	}

	public function hasAccessToken(): bool
	{
		return array_key_exists('access_token', $this->body);
	}

	public function refreshAccessToken(string $accessToken): void
	{
		if (array_key_exists('access_token', $this->body)) {
			$this->body['access_token'] = $accessToken;
		}
	}

	abstract public function processResponse(ResponseInterface $response): Response;
}
