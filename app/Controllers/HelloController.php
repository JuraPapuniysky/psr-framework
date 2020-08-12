<?php

declare(strict_types=1);

namespace App\Controllers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HelloController
{
    public function index(ServerRequestInterface $request, $name): ResponseInterface
    {
        return new JsonResponse(['message' => "Hello $name"], 200);
    }
}