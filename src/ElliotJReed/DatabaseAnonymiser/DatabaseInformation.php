<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use PDO;
use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedDatabase;

class DatabaseInformation
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
     * @return array An array of tables in the database
     * @throws UnsupportedDatabase
     */
    public function tables(): array
    {
        $query = $this->pdo->query($this->tableListSql());

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param string $tableName The name of the database table
     * @return array An array of columns in the table
     * @throws UnsupportedDatabase
     */
    public function columns(string $tableName): array
    {
        $databaseDriver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        switch ($databaseDriver) {
            case 'sqlite':
                return $this->sqliteTableColumns($tableName);
            case 'mysql':
                return $this->ansiTableColumns($tableName);
            default:
                throw new UnsupportedDatabase('Unsupported database driver: ' . $databaseDriver);
        }
    }

    /**
     * @return string The appropriate SQL query for returning a list of tables depending on the database driver used
     * @throws UnsupportedDatabase
     */
    private function tableListSql(): string
    {
        $databaseDriver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        switch ($databaseDriver) {
            case 'sqlite':
                return 'SELECT `name` FROM sqlite_master WHERE type = "table"';
            case 'mysql':
                return 'SELECT TABLE_NAME
                  FROM INFORMATION_SCHEMA.Tables';
            default:
                throw new UnsupportedDatabase('Unsupported database driver: ' . $databaseDriver);
        }
    }

    /**
     * @param string $table The table name
     * @return array An array of columns in the table
     */
    private function sqliteTableColumns(string $table): array
    {
        $tablesInfo = $this->pdo->query('PRAGMA table_info("' . $table . '")')->fetchAll();
        $columns = [];
        foreach ($tablesInfo as $tableInfo) {
            $columns[] = $tableInfo['name'];
        }

        return $columns;
    }

    /**
     * @param string $table The table name
     * @return array An array of columns in the table
     */
    private function ansiTableColumns(string $table): array
    {
        $query = $this->pdo->prepare('SELECT COLUMN_NAME
            FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ?');
        $query->execute([$table]);

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
}
