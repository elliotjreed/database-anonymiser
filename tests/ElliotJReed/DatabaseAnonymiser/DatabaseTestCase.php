<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

class DatabaseTestCase extends TestCase
{
    private $pdo;

    protected function sqlite(): DatabaseTestCase
    {
        $this->pdo = new PDO('sqlite::memory:', '', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        return $this;
    }

    protected function mysql(): DatabaseTestCase
    {
        $this->pdo = new PDO('mysql:host=mysql;dbname=database_anonymiser', 'root', 'password', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        return $this;
    }

    protected function postgres(): DatabaseTestCase
    {
        $this->pdo = new PDO('pgsql:host=postgres;dbname=database_anonymiser', 'postgres', 'password', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        return $this;
    }

    protected function getConnection(): PDO
    {
        return $this->pdo;
    }
}
