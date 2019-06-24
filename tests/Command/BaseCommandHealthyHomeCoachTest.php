<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery\Expectation;
use Monolog\Handler\NullHandler;
use Nette\Caching\Cache;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Neon\Entity;
use Ofce\Netatmo\Client\Client;
use Ofce\Netatmo\Client\Request\AuthorizationRequest;
use Ofce\Netatmo\Configuration\Configuration;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Logger\Logger;

$mockedHandler = new MockHandler([
	new Response(200, [], '{"access_token":"123456789123456789","refresh_token":"1234567891234567894213213","scope":["read_homecoach"],"expires_in":10800,"expire_in":10800}'),

	new Response(200, [], '{"body":{"devices":[{"_id":"12:12:aa:21:21:21","cipher_id":"123123123123123123213213121234231243214321123123432423","date_setup":1535396300,"last_setup":1535396321,"type":"NHC","last_status_store":1560201322,"firmware":4544,"last_upgrade":1535396472,"wifi_status":42,"reachable":true,"co2_calibrating":false,"station_name":"Healthy home coach","data_type":["Temperature","CO2","Humidity","Noise","Pressure","health_idx"],"place":{"altitude":295,"city":"Prague","country":"CZ","timezone":"Europe\/Prague","location":[15.0,50.0]},"dashboard_data":{"time_utc":1560201699,"Temperature":25.6,"CO2":0,"Humidity":54,"Noise":40,"Pressure":972,"AbsolutePressure":972,"health_idx":0,"min_temp":24.3,"max_temp":29,"date_min_temp":1560140087,"date_max_temp":1560183531}}],"user":{"mail":"testemailaddress@domain.com","administrative":{"lang":"en-US","reg_locale":"en-CZ","unit":0,"windunit":0,"pressureunit":0,"feel_like_algo":0}}},"status":"ok","time_exec":0.069116115570068,"time_server":1560202126}'),
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

$client = new Client($guzzle, $authorizationRequest, $cache, $logger);

$mockedConfiguration = Mockery::mock(Configuration::class)->makePartial();

$mockedDevice = new HealthyHomeCoach('device1', '12:12:aa:21:21:21');

$mockedConfiguration->shouldReceive([
	'getCache' => $cache,
	'getLogger' => $logger,
	'getClient' => $client,
]);

/** @var Expectation $deviceName */
$deviceName = $mockedConfiguration->shouldReceive('getDeviceByName');

$deviceName->withArgs(['device1', HealthyHomeCoach::DEVICE_NAME])
	->andReturn($mockedDevice);
