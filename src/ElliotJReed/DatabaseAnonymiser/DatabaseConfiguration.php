<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedDatabase;
use PDO;

class DatabaseConfiguration
{
    /**
     * DatabaseInformation constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(private PDO $pdo)
    {
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
     *
     * @throws UnsupportedDatabase
     */
    private function disableForeignKeyChecksSql(): string
    {
        $databaseDriver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        return match ($databaseDriver) {
            'sqlite' => 'PRAGMA foreign_keys = 0',
            'mysql' => 'SET FOREIGN_KEY_CHECKS=0',
            default => throw new UnsupportedDatabase('Unsupported database driver: ' . $databaseDriver),
        };
    }
}
