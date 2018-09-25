<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use PDO;
use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedDatabase;

class DatabaseConfiguration
{
    private $pdo;

    /**
     * DatabaseInformation constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws UnsupportedDatabase
     */
    public function disableForeignKeyChecks(): void
    {
        $this->pdo->exec($this->disableForeignKeyChecksSql());
    }

    /**
     * @return string The appropriate SQL query for disabling foreign key checks depending on the database driver used
     * @throws UnsupportedDatabase
     */
    private function disableForeignKeyChecksSql(): string
    {
        $databaseDriver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        switch ($databaseDriver) {
            case 'sqlite':
                return 'PRAGMA foreign_keys = 0';
            case 'mysql':
                return 'SET FOREIGN_KEY_CHECKS=0';
            default:
                throw new UnsupportedDatabase('Unsupported database driver: ' . $databaseDriver);
        }
    }
}
