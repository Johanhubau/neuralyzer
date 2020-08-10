<?php

use Doctrine\DBAL\Types\Type;

require_once 'vendor/autoload.php';

// Create a container
$container = Edyan\Neuralyzer\ContainerFactory::createContainer();
$expression = $container->get('Edyan\Neuralyzer\Utils\Expression');
// Configure DB Utils, required
$dbUtils = $container->get('Edyan\Neuralyzer\Utils\DBUtils');
// See Doctrine DBAL configuration :
// https://www.doctrine-project.org/projects/doctrine-dbal/en/2.7/reference/configuration.html
$dbUtils->configure([ //TODO CHANGER LES VALEURS AUX VALEURS EN BDD
    'driver' => 'pdo_mysql',
    'host' => '172.18.0.3',
    'dbname' => 'XXX',
    'user' => 'XXX',
    'password' => 'XXX',
]);

Type::addType('currency', 'Edyan\Neuralyzer\Doctrine\Type\CurrencyDoctrineType');

$reader = new \Edyan\Neuralyzer\Configuration\Reader('Elexia.yml');

$db = new \Edyan\Neuralyzer\Anonymizer\DB($expression, $dbUtils);
$db->setConfiguration($reader);
$db->setPretend(false);
// Get tables
$tables = $reader->getEntities();
foreach ($tables as $table) {
    $total = $dbUtils->countResults($table);

    fwrite(STDOUT, "Doing $table:" . PHP_EOL);

    if ($total === 0) {
        fwrite(STDOUT, "$table is empty" . PHP_EOL);
        continue;
    }
    $db->processEntity($table);

    fwrite(STDOUT, "$table anonymized" . PHP_EOL);
}