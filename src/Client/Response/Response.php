<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Client\Response;


use Nette\Schema\Processor;
use Nette\Schema\Schema;


abstract class Response
{
	/**
	 * @param mixed[] $response
	 */
	public function __construct(array $response)
	{
		$this->validateResponse($response);
		$this->createFromArrayResponse($response);
	}

	protected function validateResponse(array $response): void
	{
		(new Processor())->process($this->getResponseSchema(), $response);
	}

	protected abstract function createFromArrayResponse(array $response): void;

	protected abstract function getResponseSchema(): Schema;
}