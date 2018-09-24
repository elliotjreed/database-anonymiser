<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

class SqliteTestCase extends TestCase
{
    /** @var PDO */
    protected $pdo;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->pdo = new PDO(getenv('SQLITE_DSN'), getenv('SQLITE_USERNAME'), getenv('SQLITE_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}
