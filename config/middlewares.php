<?php
/** @var \PsrFramework\Http\Application $app */

$app->pipe(\PsrFramework\Http\Router\Router::class);
$app->pipe(\App\Middlewares\JwtAuthMiddleware::class);