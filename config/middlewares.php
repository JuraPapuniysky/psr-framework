<?php
/** @var \PsrFramework\Http\Application $app */

$app->pipe(\App\Middlewares\ErrorMiddleware::class);
$app->pipe(\App\Middlewares\JwtAuthMiddleware::class);
$app->pipe(\PsrFramework\Http\Router\Router::class);