<?php

/** @var \PsrFramework\Http\Application $app */

$app->get('hello', '/api/v1/hello/{name}', [\App\Controllers\HelloController::class, 'index']);