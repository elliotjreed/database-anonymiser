<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedDatabaseException;
use PDO;

class Information
{
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function tables(): array
    {
        $query = $this->db->prepare($this->tableListSql());
        $query->execute();

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    private function tableListSql(): string
    {
        $databaseDriver = $this->databaseDriver();
        switch ($databaseDriver) {
            case 'sqlite':
                return 'SELECT `name` FROM sqlite_master WHERE type="table"';
            case 'mysql':
                return 'SHOW TABLES';
            case 'pgsql':
                return "SELECT * FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'";
            default:
                throw new UnsupportedDatabaseException('Unsupported database driver: ' . $databaseDriver);
        }
    }

    private function databaseDriver(): string
    {
        return $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
}
