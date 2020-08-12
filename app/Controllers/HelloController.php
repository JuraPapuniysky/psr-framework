<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\HelloService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrFramework\Http\Controller\Controller;


class HelloController extends Controller
{
    private HelloService $helloService;

    public function __construct(HelloService $helloService)
    {
        $this->helloService = $helloService;
    }

    public function index(ServerRequestInterface $request, $name): ResponseInterface
    {
        return new JsonResponse([
            'message' => $this->helloService->sayHello($request->getAttribute('auth_user'))
        ], 200);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(json_decode($request->getBody()->getContents()), 200);
    }

    public function error(ServerRequestInterface $request): ResponseInterface
    {
        throw new \Exception('opps');
    }
}