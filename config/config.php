<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/** @var ContainerInterface $container */

$connection = [
    'dbname' => $_ENV['DB_DATABASE'],
    'user' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'host' => $_ENV['DB_HOST'],
    'driver' => $_ENV['DB_DRIVER'],
];

return [
    'singletones' => [
        Psr\Log\LoggerInterface::class => DI\factory(function () {
            $logger = new Logger('App');

            $fileHandler = new StreamHandler(__DIR__ . '/../log/app.log', Logger::DEBUG);
            $fileHandler->setFormatter(new LineFormatter());
            $logger->pushHandler($fileHandler);

            return $logger;
        }),
        \Doctrine\ORM\EntityManagerInterface::class => DI\factory(function () use($connection) {
            $isDevMode = true;
            $proxyDir = null;
            $cache = null;
            $useSimpleAnnotationReader = false;
            $config = Setup::createAnnotationMetadataConfiguration([__DIR__."/../app"], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
            // or if you prefer yaml or XML
            // $config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
            // $config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

            // database configuration parameters


            // obtaining the entity manager
            return EntityManager::create($connection, $config);
        }),
    ],
];