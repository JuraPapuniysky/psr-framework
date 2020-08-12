<?php

use PsrFramework\Http\Application;
use Narrowspark\HttpEmitter\SapiEmitter;;

require __DIR__ . '/../vendor/autoload.php';

$container = new \DI\Container();
$app = new Application($container);

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();

require __DIR__ . '/../config/middlewares.php';
require __DIR__ . '/../config/routes.php';

$response = $app->handle($request);

$emitter = new SapiEmitter();
$emitter->emit($response);

