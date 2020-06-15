<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Command;

use Ofce\Netatmo\Configuration\Configuration;
use Ofce\Netatmo\Logger\Logger;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
	protected Configuration $configuration;

	protected Logger $logger;

	public function __construct(Configuration $configuration)
	{
		parent::__construct(null);
		$this->configuration = $configuration;
		$this->logger = $configuration->getLogger();
	}
}
