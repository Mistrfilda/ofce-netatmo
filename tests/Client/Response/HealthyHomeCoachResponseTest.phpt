<?php

declare(strict_types=1);

use Nette\Schema\ValidationException;
use Nette\Utils\Json;
use Ofce\Netatmo\Client\Response\HealthyHomeCoachResponse;
use Tester\Assert;

require __DIR__ . '/../../Bootstrap.php';

$mockedResponse = '{"body":{"devices":[{"_id":"12:12:aa:21:21:21","cipher_id":"123123123123123123213213121234231243214321123123432423","date_setup":1535396300,"last_setup":1535396321,"type":"NHC","last_status_store":1560201322,"firmware":4544,"last_upgrade":1535396472,"wifi_status":42,"reachable":true,"co2_calibrating":false,"station_name":"Healthy home coach","data_type":["Temperature","CO2","Humidity","Noise","Pressure","health_idx"],"place":{"altitude":295,"city":"Prague","country":"CZ","timezone":"Europe\/Prague","location":[15.0,50.0]},"dashboard_data":{"time_utc":1560201699,"Temperature":25.6,"CO2":0,"Humidity":54,"Noise":40,"Pressure":972,"AbsolutePressure":972,"health_idx":0,"min_temp":24.3,"max_temp":29,"date_min_temp":1560140087,"date_max_temp":1560183531}}],"user":{"mail":"testemailaddress@domain.com","administrative":{"lang":"en-US","reg_locale":"en-CZ","unit":0,"windunit":0,"pressureunit":0,"feel_like_algo":0}}},"status":"ok","time_exec":0.069116115570068,"time_server":1560202126}';


$parsedJson = Json::decode($mockedResponse, Json::FORCE_ARRAY);

Assert::count(4, $parsedJson);

$healthyHomeCoachReponse = new HealthyHomeCoachResponse($parsedJson);

Assert::equal(1560202126, $healthyHomeCoachReponse->getDeviceTime()->getTimestamp());

Assert::equal(0.069116115570068, $healthyHomeCoachReponse->getExecutionTime());
Assert::equal('ok', $healthyHomeCoachReponse->getStatus());

$healthyHomeCoachData = $healthyHomeCoachReponse->getHealthyHomeCoachData();

Assert::equal(0.0, $healthyHomeCoachData->getCO2());
Assert::equal(54.0, $healthyHomeCoachData->getHumidity());
Assert::equal(25.6, $healthyHomeCoachData->getTemperature());
Assert::equal(1560201699, $healthyHomeCoachData->getTime()->getTimestamp());
Assert::equal(24.3, $healthyHomeCoachData->getMinTemp());
Assert::equal(1560140087, $healthyHomeCoachData->getDateMinTemp()->getTimestamp());
Assert::equal(29.0, $healthyHomeCoachData->getMaxTemp());
Assert::equal(1560183531, $healthyHomeCoachData->getDateMaxTemp()->getTimestamp());
Assert::equal(40.0, $healthyHomeCoachData->getNoise());
Assert::equal(972.0, $healthyHomeCoachData->getPressure());
Assert::equal(972.0, $healthyHomeCoachData->getAbsolutePressure());
Assert::equal(0, $healthyHomeCoachData->getHealthIdx());

$consoleOutput = $healthyHomeCoachData->getConsoleOutput();
Assert::count(10, $consoleOutput);
Assert::equal('Time', $consoleOutput[0][0]);
Assert::equal('2019-06-10 21:21:39', $consoleOutput[0][1]);
Assert::equal('25.6 °C', $consoleOutput[1][1]);
Assert::equal('Min temp', $consoleOutput[8][0]);
Assert::equal('24.3 °C at 2019-06-10 04:14:47', $consoleOutput[8][1]);

$invalidMockedResponse = '{"body":{"devices":[{"_id":"12:12:aa:21:21:21","cipher_id":"12312312312312312321321312VuT42312dPZzUv1TjrTXMf432423","date_setup":1535396300,"last_setup":1535396321,"type":"NHC","last_status_store":1560201700,"firmware":45,"last_upgrade":1535396472,"wifi_status":44,"reachable":true,"co2_calibrating":false,"station_name":"Healthy home coach","data_type":["Temperature","CO2","Humidity","Noise","Pressure","health_idx"],"place":{"altitude":295,"city":"Prague","country":"CZ","timezone":"Europe\/Prague","location":[15.0,50.0]},"dashboard_data":{"time_utc":1560201699,"CO2":0,"Humidity":54,"Noise":40,"Pressure":972,"AbsolutePressure":972,"health_idx":0,"min_temp":24.3,"max_temp":29,"date_min_temp":1560140087,"date_max_temp":1560183531}}],"user":{"mail":"testemailaddress@domain.com","administrative":{"lang":"en-US","reg_locale":"en-CZ","unit":0,"windunit":0,"pressureunit":0,"feel_like_algo":0}}},"status":"ok","time_exec":0.069116115570068,"time_server":1560202126}';


$parsedJson = Json::decode($invalidMockedResponse, Json::FORCE_ARRAY);

Assert::exception(function () use ($parsedJson): void {
	new HealthyHomeCoachResponse($parsedJson);
}, ValidationException::class, "The mandatory option 'Temperature' is missing.");
