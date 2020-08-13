<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use PsrFramework\Adapters\UuidGenerator\UuidGeneratorInterface;
use PsrFramework\Factories\UuidGeneratorFactory;
use PsrFramework\Adapters\PasswordHash\PasswordHashInterface;
use PsrFramework\Factories\PasswordHashFactory;
use App\Factories\Entity\Request\RequestEntityFactoryInterface;
use App\Factories\Entity\Request\RequestEntityFactory;

/** @var ContainerInterface $container */

$connection = require __DIR__ . '/db_connection.php';

return [
    'dependencies' => [
        LoggerInterface::class => DI\factory(function () {
            $logger = new Logger('App');

            $fileHandler = new StreamHandler(__DIR__ . '/../log/app.log', Logger::DEBUG);
            $fileHandler->setFormatter(new LineFormatter());
            $logger->pushHandler($fileHandler);

            return $logger;
        }),
        EntityManagerInterface::class => DI\factory(function () use($connection) {
            $isDevMode = true;
            $proxyDir = null;
            $cache = null;
            $useSimpleAnnotationReader = false;
            $config = Setup::createAnnotationMetadataConfiguration([__DIR__."/../app"], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

            return EntityManager::create($connection, $config);
        }),
        UuidGeneratorInterface::class => (new UuidGeneratorFactory())(),
        PasswordHashInterface::class => (new PasswordHashFactory())(),
        RequestEntityFactoryInterface::class => DI\factory(function () {
            $factory = new RequestEntityFactory();
            return $factory;
        })
    ],
];