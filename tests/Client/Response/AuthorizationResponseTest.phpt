<?php

use Nette\Schema\ValidationException;
use Nette\Utils\Json;
use Ofce\Netatmo\Client\Response\AuthorizationResponse;
use Tester\Assert;


require __DIR__ . '/../../Bootstrap.php';

$mockedResponse = '{"access_token":"123455612312fe06a38b4ff8|da4c9ed0f799d1a72fd6ffee832ed6c8","refresh_token":"321321321|542354214e01128801e7485a93213215dcc","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}';


$parsedJson = Json::decode($mockedResponse, Json::FORCE_ARRAY);

Assert::count(5, $parsedJson);

$authorizationReponse = new AuthorizationResponse($parsedJson);

Assert::equal(
	'123455612312fe06a38b4ff8|da4c9ed0f799d1a72fd6ffee832ed6c8',
	$authorizationReponse->getAccessToken()
);

Assert::equal(
	'321321321|542354214e01128801e7485a93213215dcc',
	$authorizationReponse->getRefreshToken()
);

Assert::equal(['read_homecoach'], $authorizationReponse->getScope());

Assert::equal(10800, $authorizationReponse->getExpireIn());
Assert::equal(10800, $authorizationReponse->getExpiresIn());


$invalidMockedResponse = '{"refresh_token":"321321321|542354214e01128801e7485a93213215dcc","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}';


$parsedJson = Json::decode($invalidMockedResponse, Json::FORCE_ARRAY);

Assert::exception(function() use ($parsedJson) {
	$authorizationReponse = new AuthorizationResponse($parsedJson);
}, ValidationException::class, "The mandatory option 'access_token' is missing.");