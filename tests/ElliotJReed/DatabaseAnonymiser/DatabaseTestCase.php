<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

class DatabaseTestCase extends TestCase
{
    /** @var PDO */
    protected $pdo;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->pdo = new PDO(\getenv('DB_DSN'), \getenv('DB_USERNAME'), \getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}
