<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Command\HealthyHomeCoach;

use Ofce\Netatmo\Command\BaseCommand;
use Ofce\Netatmo\Configuration\Configuration;
use Ofce\Netatmo\Facade\HealthyHomeCoachFacade;

abstract class BaseHealthyHomeCoachCommand extends BaseCommand
{
	protected HealthyHomeCoachFacade $healthyHomeCoachFacade;

	public function __construct(Configuration $configuration)
	{
		parent::__construct($configuration);
		$this->healthyHomeCoachFacade = new HealthyHomeCoachFacade($configuration);
	}
}
