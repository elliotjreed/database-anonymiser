#!/usr/bin/env php
<?php

declare(strict_types=1);

use ElliotJReed\DatabaseAnonymiser\ConfigurationFileParser;
use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;
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

    (new Validator(new DatabaseInformation($pdo)))->validateConfiguration($configuration['anonymise']);

    echo 'Configuration file is valid!' . PHP_EOL;
    exit(0);
} catch (ConfigurationFile $exception) {
    echo $exception->getMessage() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

exit(1);
