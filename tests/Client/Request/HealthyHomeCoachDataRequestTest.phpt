<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Ofce\Netatmo\Client\Request\HealthyHomeCoachDataRequest;
use Ofce\Netatmo\Client\Request\Request;
use Tester\Assert;


require __DIR__ . '/../../Bootstrap.php';

$authorizationRequest = new HealthyHomeCoachDataRequest(
	'123123123123',
	'12:12:aa:aa:21:21'
);

Assert::truthy($authorizationRequest->hasBody());
Assert::count(2, $authorizationRequest->getBody());
Assert::truthy(array_key_exists('access_token', $authorizationRequest->getBody()));
Assert::truthy(array_key_exists('device_id', $authorizationRequest->getBody()));

$httpRequest = $authorizationRequest->getHttpRequest();
Assert::equal('api/gethomecoachsdata', $httpRequest->getUri()->getPath());
Assert::equal(Request::METHOD_POST, Strings::lower($httpRequest->getMethod()));