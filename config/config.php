<?php

use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/** @var ContainerInterface $container */

return [
    'singletones' => [
        Psr\Log\LoggerInterface::class => DI\factory(function () {
            $logger = new Logger('App');

            $fileHandler = new StreamHandler(__DIR__ . '/../log/app.log', Logger::DEBUG);
            $fileHandler->setFormatter(new LineFormatter());
            $logger->pushHandler($fileHandler);

            return $logger;
        }),
    ],
];