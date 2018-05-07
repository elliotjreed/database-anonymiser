<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

class DatabaseTestCase extends TestCase
{
    /* @var PDO */
    protected $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', '', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}
