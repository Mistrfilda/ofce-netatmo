<?php

declare(strict_types=1);

use Ofce\Netatmo\Lib\DatetimeFactory;
use Tester\Assert;

require __DIR__ . '/../Bootstrap.php';


$datetimeFactory = new DatetimeFactory();

Assert::equal((new DateTimeImmutable())->getTimestamp(), $datetimeFactory->createNow()->getTimestamp());
