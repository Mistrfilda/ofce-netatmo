<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Logger;

use InvalidArgumentException;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger as MonologLogger;
use Nette\Neon\Entity;
use ReflectionClass;
use ReflectionException;
use Throwable;

class Logger
{
	private MonologLogger $logger;

	/**
	 * Logger constructor.
	 * @param Entity[] $handlers
	 * @throws ReflectionException
	 */
	public function __construct(string $name, array $handlers)
	{
		$this->logger = new MonologLogger($name);

		//pass handlers from config
		foreach ($handlers as $handler) {
			$reflection = new ReflectionClass($handler->value);
			$handler = $reflection->newInstanceArgs($handler->attributes);

			if (! $handler instanceof HandlerInterface) {
				throw new InvalidArgumentException('Invalid handler specified for monolog');
			}

			$this->logger->pushHandler($handler);
		}
	}

	/**
	 * @param mixed[] $context
	 */
	public function addDebug(string $message, array $context = []): void
	{
		$this->logger->addDebug($message, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function addInfo(string $message, array $context = []): void
	{
		$this->logger->addInfo($message, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function addNotice(string $message, array $context = []): void
	{
		$this->logger->addNotice($message, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function addWarning(string $message, array $context = []): void
	{
		$this->logger->addWarning($message, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function addCritical(string $message, array $context = []): void
	{
		$this->logger->addCritical($message, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function addEmergency(string $message, array $context = []): void
	{
		$this->logger->addEmergency($message, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function addException(Throwable $exception, array $context = []): void
	{
		$this->logger->addCritical($exception->getMessage(), array_merge(['exception' => $exception], $context));
	}
}
