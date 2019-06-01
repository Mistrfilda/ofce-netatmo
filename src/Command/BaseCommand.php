<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Command;


use Ofce\Netatmo\Configuration\Configuration;
use Symfony\Component\Console\Command\Command;


abstract class BaseCommand extends Command
{
	/** @var Configuration */
	protected $configuration;

	public function __construct(Configuration $configuration)
	{
		parent::__construct( null);
		$this->configuration = $configuration;
	}
}