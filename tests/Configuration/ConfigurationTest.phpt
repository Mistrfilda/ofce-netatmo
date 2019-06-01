<?php

use Nette\Schema\ValidationException;
use Ofce\Netatmo\Configuration\Configuration;
use Ofce\Netatmo\Device\HealthyHomeCoach;
use Ofce\Netatmo\Exception\UnknownDeviceException;
use Tester\Assert;


require __DIR__ . '/../Bootstrap.php';


Assert::exception(function () {
	new Configuration(__DIR__ . '/wrong.config.neon');
}, ValidationException::class);


$configuration = new Configuration(__DIR__ . '/sample.config.neon');

Assert::count(2, $configuration->getDevices());

Assert::equal('https://testapi.netatmo.com/', $configuration->getClient()->getApiUrl());

$device = $configuration->getDeviceByName('room1');

Assert::type(HealthyHomeCoach::class, $device);
Assert::equal('88:5e:aa:11:22:33', $device->getMacAddress());
Assert::equal(HealthyHomeCoach::DEVICE_NAME, $device->getDeviceType());
Assert::equal('room1', $device->getName());

$device = $configuration->getDeviceByName('room1', HealthyHomeCoach::DEVICE_NAME);
Assert::type(HealthyHomeCoach::class, $device);


Assert::exception(function () use ($configuration) {
	$configuration->getDeviceByName('room123');
}, UnknownDeviceException::class);


Assert::exception(function () use ($configuration) {
	$configuration->getDeviceByName('room1', 'unknwondDeviceType');
}, UnknownDeviceException::class);