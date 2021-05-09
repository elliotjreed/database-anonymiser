#!/usr/bin/env php
<?php

declare(strict_types=1);

use ElliotJReed\DatabaseAnonymiser\Anonymiser;
use ElliotJReed\DatabaseAnonymiser\ConfigurationFileParser;
use ElliotJReed\DatabaseAnonymiser\DatabaseConfiguration;
use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Validator;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'Please specify a YAML, JSON, or PHP configuration file.' . PHP_EOL;
    exit(1);
}

try {
    $configuration = (new ConfigurationFileParser(new SplFileObject($argv[1], 'rb')))->toArray();
    $connection = $configuration['database-connection'];

    $pdo = new PDO($connection['dsn'], $connection['username'], $connection['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    (new Anonymiser($pdo, new DatabaseConfiguration($pdo), new Validator(new DatabaseInformation($pdo))))->anonymise($configuration['anonymise']);

    echo 'Anonymisation complete! Remember to check all tables manually for potentially sensitive data which may have been missing from your configuration.' . PHP_EOL;
    exit(0);
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

exit(1);
