<?php

declare(strict_types=1);

namespace Ofce\Netatmo\Lib;

use DateTimeImmutable;

class DatetimeFactory
{
	public const DATETIME_FORMAT = 'Y-m-d H:i:s';

	public function createNow(): DateTimeImmutable
	{
		return new DateTimeImmutable();
	}
}
