#!/usr/bin/env php
<?php
declare(strict_types=1);

use ElliotJReed\DatabaseAnonymiser\ConfigurationFileParser;
use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Validator;
use ElliotJReed\DatabaseAnonymiser\Anonymiser;

require __DIR__ . '/../vendor/autoload.php';

if (isset($argv[1])) {
    $configFile = $argv[1];
} else {
    exit('Please specify a YAML, JSON, or PHP configuration file.' . PHP_EOL);
}

$configuration = (new ConfigurationFileParser(new SplFileObject($configFile, 'r')))->toArray();
$connection = $configuration['database-connection'];

$pdo = new PDO($connection['dsn'], $connection['username'], $connection['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

(new Anonymiser($pdo, new Validator(new DatabaseInformation($pdo))))
    ->anonymise($configuration['anonymise']);
