<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Client\Response;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

class AuthorizationResponse extends Response
{
	private string $accessToken;

	private string $refreshToken;

	/**
	 * @var string[]
	 */
	private array $scope;

	private int $expiresIn;

	private int $expireIn;

	public function getAccessToken(): string
	{
		return $this->accessToken;
	}

	public function getRefreshToken(): string
	{
		return $this->refreshToken;
	}

	/**
	 * @return string[]
	 */
	public function getScope(): array
	{
		return $this->scope;
	}

	public function getExpiresIn(): int
	{
		return $this->expiresIn;
	}

	public function getExpireIn(): int
	{
		return $this->expireIn;
	}

	/**
	 * @param mixed[] $response
	 */
	protected function createFromArrayResponse(array $response): void
	{
		$this->accessToken = $response['access_token'];
		$this->refreshToken = $response['refresh_token'];
		$this->scope = $response['scope'];
		$this->expiresIn = $response['expires_in'];
		$this->expireIn = $response['expire_in'];
	}

	protected function getResponseSchema(): Schema
	{
		return Expect::structure([
			'access_token' => Expect::string()->required()->min(10.0),
			'refresh_token' => Expect::string()->required()->min(10.0),
			'scope' => Expect::array()->required(),
			'expires_in' => Expect::int()->required(),
			'expire_in' => Expect::int()->required(),
		]);
	}
}
