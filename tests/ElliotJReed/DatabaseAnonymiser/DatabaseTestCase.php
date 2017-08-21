<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use PHPUnit\Framework\TestCase;
use PDO;

class DatabaseTestCase extends TestCase
{
    private $db;

    public function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
    }
}
