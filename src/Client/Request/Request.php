<?php

declare(strict_types = 1);

namespace Ofce\Netatmo\Client\Request;

use App\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

abstract class Request
{
	public const METHOD_GET = 'get';

	public const METHOD_POST = 'post';

	/** @var GuzzleRequest */
	private $httpRequest;

	/**
	 * Request constructor.
	 * @param string $method
	 * @param string $endpoint
	 * @param array $body
	 * @throws RequestException
	 */
	public function __construct(string $method, string $endpoint, array $body)
	{
		if (!in_array($method, [self::METHOD_GET, self::METHOD_POST])) {
			throw new RequestException('Unsupported METHOD');
		}

		$this->httpRequest = new GuzzleRequest(
			$method,
			$endpoint,
			[],
			$body
		);
	}

	public function getHttpRequest(): GuzzleRequest
	{
		return $this->httpRequest;
	}
}