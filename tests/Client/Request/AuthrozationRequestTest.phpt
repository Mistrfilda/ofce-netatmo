<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Client\Request\Request;
use Tester\Assert;

require __DIR__ . '/../../Bootstrap.php';

$authorizationRequest = new AuthorizationRequest(
	'123123',
	'456456',
	'test@randomemailaddress.com',
	'strongpassword',
	'healthy_home_coach'
);

Assert::truthy($authorizationRequest->hasBody());
Assert::count(6, $authorizationRequest->getBody());
Assert::truthy(array_key_exists('grant_type', $authorizationRequest->getBody()));

$httpRequest = $authorizationRequest->getHttpRequest();

Assert::equal('oauth2/token', $httpRequest->getUri()->getPath());
Assert::equal(Request::METHOD_POST, Strings::lower($httpRequest->getMethod()));