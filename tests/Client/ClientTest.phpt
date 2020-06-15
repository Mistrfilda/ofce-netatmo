<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\NullHandler;
use Nette\Caching\Cache;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Neon\Entity;
use Nette\Schema\ValidationException;
use Ofce\Netatmo\Client\Client;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Client\Request\HealthyHomeCoachDataRequest;
use Ofce\Netatmo\Client\Response\AuthorizationResponse;
use Ofce\Netatmo\Client\Response\HealthyHomeCoachResponse;
use Ofce\Netatmo\Exception\RequestException as OfceRequestException;
use Ofce\Netatmo\Logger\Logger;
use Tester\Assert;

require __DIR__ . '/../Bootstrap.php';


$mockedHandler = new MockHandler([
	//AUTHROZATION MOCK
	new Response(
		200,
		[],
		'{"access_token":"123456789123456789","refresh_token":"1234567891234567894213213","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'
	),
	new Response(
		200,
		[],
		'{"access_token":"123123123","refresh_token":"321321321321321","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'
	),
	new Response(
		200,
		[],
		'{"access_token":"123456789123456789123456","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'
	),
	new Response(
		300,
		[],
		'{"access_token":"123456789123456789123456","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'
	),

	//Request Exception
	new RequestException('Error test due to invalid request', new Request('GET', 'test')),
	new RequestException('Error test due to server error', new Request('GET', 'test')),

	//Get healthy home coach data request
	new Response(
		200,
		[],
		'{"body":{"devices":[{"_id":"12:12:aa:21:21:21","cipher_id":"123123123123123123213213121234231243214321123123432423","date_setup":1535396300,"last_setup":1535396321,"type":"NHC","last_status_store":1560201322,"firmware":4544,"last_upgrade":1535396472,"wifi_status":42,"reachable":true,"co2_calibrating":false,"station_name":"Healthy home coach","data_type":["Temperature","CO2","Humidity","Noise","Pressure","health_idx"],"place":{"altitude":295,"city":"Prague","country":"CZ","timezone":"Europe\/Prague","location":[15.0,50.0]},"dashboard_data":{"time_utc":1560201699,"Temperature":25.6,"CO2":0,"Humidity":54,"Noise":40,"Pressure":972,"AbsolutePressure":972,"health_idx":0,"min_temp":24.3,"max_temp":29,"date_min_temp":1560140087,"date_max_temp":1560183531}}],"user":{"mail":"testemailaddress@domain.com","administrative":{"lang":"en-US","reg_locale":"en-CZ","unit":0,"windunit":0,"pressureunit":0,"feel_like_algo":0}}},"status":"ok","time_exec":0.069116115570068,"time_server":1560202126}'
	),
	new Response(
		200,
		[],
		'{"body":{"devices":[{"_id":"12:12:aa:21:21:21","cipher_id":"12312312312312312321321312VuT42312dPZzUv1TjrTXMf432423","date_setup":1535396300,"last_setup":1535396321,"type":"NHC","last_status_store":1560201700,"firmware":45,"last_upgrade":1535396472,"wifi_status":44,"reachable":true,"co2_calibrating":false,"station_name":"Healthy home coach","data_type":["Temperature","CO2","Humidity","Noise","Pressure","health_idx"],"place":{"altitude":295,"city":"Prague","country":"CZ","timezone":"Europe\/Prague","location":[15.0,50.0]},"dashboard_data":{"time_utc":1560201699,"CO2":0,"Humidity":54,"Noise":40,"Pressure":972,"AbsolutePressure":972,"health_idx":0,"min_temp":24.3,"max_temp":29,"date_min_temp":1560140087,"date_max_temp":1560183531}}],"user":{"mail":"testemailaddress@domain.com","administrative":{"lang":"en-US","reg_locale":"en-CZ","unit":0,"windunit":0,"pressureunit":0,"feel_like_algo":0}}},"status":"ok","time_exec":0.069116115570068,"time_server":1560202126}'
	),

	//Resend due to invalid authorization code
	new ClientException('Error test', new Request('GET', 'test'), new Response(403)),
	new Response(
		200,
		[],
		'{"access_token":"123456789123456789","refresh_token":"321321321|542354214e01128801e7485a93213215dcc","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'
	),
	new Response(
		200,
		[],
		'{"body":{"devices":[{"_id":"12:12:aa:21:21:21","cipher_id":"123123123123123123213213121234231243214321123123432423","date_setup":1535396300,"last_setup":1535396321,"type":"NHC","last_status_store":1560201322,"firmware":4544,"last_upgrade":1535396472,"wifi_status":42,"reachable":true,"co2_calibrating":false,"station_name":"Healthy home coach","data_type":["Temperature","CO2","Humidity","Noise","Pressure","health_idx"],"place":{"altitude":295,"city":"Prague","country":"CZ","timezone":"Europe\/Prague","location":[15.0,50.0]},"dashboard_data":{"time_utc":1560201699,"Temperature":25.6,"CO2":0,"Humidity":54,"Noise":40,"Pressure":972,"AbsolutePressure":972,"health_idx":0,"min_temp":24.3,"max_temp":29,"date_min_temp":1560140087,"date_max_temp":1560183531}}],"user":{"mail":"testemailaddress@domain.com","administrative":{"lang":"en-US","reg_locale":"en-CZ","unit":0,"windunit":0,"pressureunit":0,"feel_like_algo":0}}},"status":"ok","time_exec":0.069116115570068,"time_server":1560202126}'
	),

	//Resend failed
	new ClientException('Error test', new Request('GET', 'test'), new Response(403)),
	new ClientException('Error due to invalid api url', new Request('GET', 'test'), new Response(500)),


	//Resend - authorization succeeded, healthy home coach data request failed
	new ClientException('Error test', new Request('GET', 'test'), new Response(403)),
	new Response(
		200,
		[],
		'{"access_token":"123456789123456789","refresh_token":"123456789123456789321321321","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'
	),
	new ClientException('Error test, invalid device mac address', new Request('GET', 'test'), new Response(500)),
]);

$guzzle = new GuzzleClient(['handler' => HandlerStack::create($mockedHandler)]);

$cache = new Cache(new DevNullStorage());

$logger = new Logger('ofce-netmo-test', [new Entity(NullHandler::class)]);

$authorizationRequest = new AuthorizationRequest(
	'123123',
	'456456',
	'test@randomemailaddress.com',
	'strongpassword',
	'healthy_home_coach'
);

$healthyHomeCoachDataRequest = new HealthyHomeCoachDataRequest('12345', '11:22:33:11:22:33');

$client = new Client($guzzle, $authorizationRequest, $cache, $logger);

/** @var AuthorizationResponse $response */
$response = $client->sendRequest($authorizationRequest);

//Valid authorization
Assert::truthy($response instanceof AuthorizationResponse);
Assert::same('123456789123456789', $response->getAccessToken());

//Invalid authrozation response
Assert::exception(function () use ($client, $authorizationRequest): void {
	$client->sendRequest($authorizationRequest);
}, ValidationException::class, 'The option \'access_token\' expects to be string in range 10.., string \'123123123\' given.');

Assert::exception(function () use ($client, $authorizationRequest): void {
	$client->sendRequest($authorizationRequest);
}, ValidationException::class, 'The mandatory option \'refresh_token\' is missing.');

Assert::exception(function () use ($client, $authorizationRequest): void {
	$client->sendRequest($authorizationRequest);
}, OfceRequestException::class, 'Returned http code 300');

//Request exception
Assert::exception(function () use ($client, $authorizationRequest): void {
	$client->sendRequest($authorizationRequest);
}, RequestException::class, 'Error test due to invalid request');


Assert::exception(function () use ($client, $healthyHomeCoachDataRequest): void {
	$client->sendRequest($healthyHomeCoachDataRequest);
}, RequestException::class, 'Error test due to server error');


//Healthy home coach data request
/** @var HealthyHomeCoachResponse $healthyHomeCoachDataResponse */
$healthyHomeCoachDataResponse = $client->sendRequest($healthyHomeCoachDataRequest);

Assert::true($healthyHomeCoachDataResponse instanceof HealthyHomeCoachResponse);

$healthyHomeCoachData = $healthyHomeCoachDataResponse->getHealthyHomeCoachData();

Assert::equal(0.0, $healthyHomeCoachData->getCO2());
Assert::equal(54.0, $healthyHomeCoachData->getHumidity());
Assert::equal(25.6, $healthyHomeCoachData->getTemperature());
Assert::equal(1560201699, $healthyHomeCoachData->getTime()->getTimestamp());
Assert::equal(24.3, $healthyHomeCoachData->getMinTemp());
Assert::equal(1560140087, $healthyHomeCoachData->getDateMinTemp()->getTimestamp());
Assert::equal(29.0, $healthyHomeCoachData->getMaxTemp());
Assert::equal(1560183531, $healthyHomeCoachData->getDateMaxTemp()->getTimestamp());
Assert::equal(40.0, $healthyHomeCoachData->getNoise());


Assert::exception(function () use ($client, $healthyHomeCoachDataRequest): void {
	$client->sendRequest($healthyHomeCoachDataRequest);
}, ValidationException::class, "The mandatory option 'Temperature' is missing.");


//Resend of healthy home coach data due to invalid token
/** @var HealthyHomeCoachResponse $healthyHomeCoachDataResponse */
$healthyHomeCoachDataResponse = $client->sendRequest($healthyHomeCoachDataRequest);

Assert::true($healthyHomeCoachDataResponse instanceof HealthyHomeCoachResponse);

$healthyHomeCoachData = $healthyHomeCoachDataResponse->getHealthyHomeCoachData();

Assert::equal(0.0, $healthyHomeCoachData->getCO2());
Assert::equal(54.0, $healthyHomeCoachData->getHumidity());
Assert::equal(25.6, $healthyHomeCoachData->getTemperature());
Assert::equal(1560201699, $healthyHomeCoachData->getTime()->getTimestamp());
Assert::equal(24.3, $healthyHomeCoachData->getMinTemp());
Assert::equal(1560140087, $healthyHomeCoachData->getDateMinTemp()->getTimestamp());
Assert::equal(29.0, $healthyHomeCoachData->getMaxTemp());
Assert::equal(1560183531, $healthyHomeCoachData->getDateMaxTemp()->getTimestamp());
Assert::equal(40.0, $healthyHomeCoachData->getNoise());

Assert::exception(function () use ($client, $healthyHomeCoachDataRequest): void {
	$client->sendRequest($healthyHomeCoachDataRequest);
}, RequestException::class, 'Error due to invalid api url');


Assert::exception(function () use ($client, $healthyHomeCoachDataRequest): void {
	$client->sendRequest($healthyHomeCoachDataRequest);
}, RequestException::class, 'Error test, invalid device mac address');
