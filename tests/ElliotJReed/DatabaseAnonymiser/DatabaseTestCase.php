<?php

declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

abstract class DatabaseTestCase extends TestCase
{
    protected PDO $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}
