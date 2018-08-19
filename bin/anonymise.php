#!/usr/bin/env php
<?php
declare(strict_types=1);

use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Validator;
use ElliotJReed\DatabaseAnonymiser\Anonymiser;

require __DIR__ . '/../vendor/autoload.php';

if (isset($argv[1])) {
    $configFile = $argv[1];
} else {
    exit('Please specify a YAML configuration file.');
}

$pdo = new PDO(getenv('DB_DSN'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$databaseInformation = new DatabaseInformation($pdo);
$configurationValidator = new Validator($pdo, $databaseInformation);
$anonymiser = (new Anonymiser($pdo, $configurationValidator));

$anonymiser->anonymise(yaml_parse_file($configFile));
