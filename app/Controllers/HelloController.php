<?php

declare(strict_types=1);

namespace App\Controllers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrFramework\Http\Controller\Controller;

class HelloController extends Controller
{
    public function index(ServerRequestInterface $request, $name): ResponseInterface
    {
        return new JsonResponse(['message' => "Hello $name", 'auth_user' => $request->getAttribute('auth_user')], 200);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        throw new \Exception('opps');
        return new JsonResponse(json_decode($request->getBody()->getContents()), 200);
    }
}