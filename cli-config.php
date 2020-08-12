<?php
require_once __DIR__.'/bootstrap.php';

/** @var array $connection */
/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\DriverManager;

$connection = DriverManager::getConnection($connection);

return new HelperSet([
    'em' => new EntityManagerHelper($entityManager),
    'db' => new ConnectionHelper($connection),
]);
