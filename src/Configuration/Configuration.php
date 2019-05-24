<?php

declare(strict_types = 1);


namespace Ofce\Netatmo\Configuration;


use App\Exception\ConfigurationException;
use Nette\Neon\Neon;


final class Configuration
{
	public function __construct()
	{
		$neonConfig = @file_get_contents(__DIR__ . '/config.local.neon');
		if ($neonConfig === false) {
			throw new ConfigurationException('Missing config file src/Configuration/config.local.neon');
		}

		$parameters = Neon::decode($neonConfig);
	}
}