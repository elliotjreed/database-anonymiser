<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

class MysqlTestCase extends TestCase
{
    /** @var PDO */
    protected $pdo;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->pdo = new PDO(\getenv('MYSQL_DSN'), \getenv('MYSQL_USERNAME'), \getenv('MYSQL_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}
