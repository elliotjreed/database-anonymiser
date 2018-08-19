<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use PDO;
use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedDatabaseException;

class DatabaseInformation
{
    private $db;

    /**
     * @param PDO $pdo
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return array
     * @throws UnsupportedDatabaseException
     */
    public function tables(): array
    {
        $query = $this->db->query($this->tableListSql());

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function columns(string $tableName): array
    {
        $query = $this->columnList($tableName);

        return $query;
    }

    /**
     * @return string
     * @throws UnsupportedDatabaseException
     */
    private function tableListSql(): string
    {
        $databaseDriver = $this->databaseDriver();
        switch ($databaseDriver) {
            case 'sqlite':
                return 'SELECT `name` FROM sqlite_master WHERE type = "table"';
            case 'mysql':
                return 'SELECT TABLE_NAME
                  FROM INFORMATION_SCHEMA.Tables';
            default:
                throw new UnsupportedDatabaseException('Unsupported database driver: ' . $databaseDriver);
        }
    }

    /**
     * @return string
     * @throws UnsupportedDatabaseException
     */
    private function columnList(string $table): array
    {
        $databaseDriver = $this->databaseDriver();
        switch ($databaseDriver) {
            case 'sqlite':
                return $this->sqliteTableColumns($table);
            case 'mysql':
                return $this->ansiTableColumns($table);
            default:
                throw new UnsupportedDatabaseException('Unsupported database driver: ' . $databaseDriver);
        }
    }

    /**
     * @return string
     */
    private function databaseDriver(): string
    {
        return $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @param string $table
     * @return array
     */
    private function sqliteTableColumns(string $table): array
    {
        $result = $this->db->query('PRAGMA table_info("' . $table . '")');
        $columns = [];
        foreach ($result->fetchAll() as $table) {
            $columns[] = $table['name'];
        }

        return $columns;
    }

    /**
     * @param string $table
     * @return array
     */
    private function ansiTableColumns(string $table): array
    {
        $query = $this->db->prepare('SELECT COLUMN_NAME
            FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ?');
        $query->execute([$table]);

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
}
