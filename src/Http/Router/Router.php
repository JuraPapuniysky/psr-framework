<?php

declare(strict_types=1);

namespace PsrFramework\Http\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Invoker\InvokerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function FastRoute\simpleDispatcher;

class Router implements MiddlewareInterface
{
    private InvokerInterface $invoker;

    private RouteCollector $routerCollector;

    private array $routes = [];


    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    public function get(string $name, string $pattern, array $handler): void
    {
        $this->routes[] = ['httpMethod' => 'GET', 'route' => $pattern, 'handler' => $handler];
    }

    public function post(string $name, string $pattern, array $handler): void
    {
        $this->routes[] = ['httpMethod' => 'POST', 'route' => $pattern, 'handler' => $handler];
    }

    public function put(string $name, string $pattern, array $handler): void
    {
        $this->routes[] = ['httpMethod' => 'PUT', 'route' => $pattern, 'handler' => $handler];
    }

    public function patch(string $name, string $pattern, array $handler): void
    {
        $this->routes[] = ['httpMethod' => 'PATCH', 'route' => $pattern, 'handler' => $handler];
    }

    public function delete(string $name, string $pattern, array $handler): void
    {
        $this->routes[] = ['httpMethod' => 'DELETE', 'route' => $pattern, 'handler' => $handler];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $router) {
            foreach ($this->routes as $route) {
                $router->addRoute($route['httpMethod'], $route['route'], $route['handler']);
            }
        });

        $httpMethod = $request->getMethod();
        $uri = $request->getUri()->getPath();

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $code = 404;
                $message = 'Not found';

                return new JsonResponse($message, $code);
            case Dispatcher::METHOD_NOT_ALLOWED:
                $code = 403;
                $message = 'Method not allowed';

                return new JsonResponse($message, $code);
            case Dispatcher::FOUND:
                $controllerHandler = $routeInfo[1];
                $vars['request'] = $request;
                foreach ($routeInfo[2] as $varName => $value) {
                    $vars[$varName] = $value;
                }

                return $this->invoker->call($controllerHandler, $vars);
        }
    }
}