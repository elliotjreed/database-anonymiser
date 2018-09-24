<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;
use PDO;

class Validator
{
    private $pdo;
    private $databaseInformation;

    /**
     * Validator constructor.
     * @param PDO $pdo
     * @param DatabaseInformation $databaseInformation
     */
    public function __construct(PDO $pdo, DatabaseInformation $databaseInformation)
    {
        $this->pdo = $pdo;
        $this->databaseInformation = $databaseInformation;
    }

    /**
     * @param array $tablesConfiguration An array of table names as keys with their corresponding configurations as values
     * @return void
     * @throws Exceptions\ConfigurationFile
     * @throws Exceptions\UnsupportedDatabase
     */
    public function validateConfiguration(array $tablesConfiguration): void
    {
        $this->checkTablesExist(array_keys($tablesConfiguration));
        $this->columnsExist($tablesConfiguration);
    }

    /**
     * @param array $tableNames An array of table names
     * @return void
     * @throws Exceptions\UnsupportedDatabase
     * @throws Exceptions\ConfigurationFile
     */
    private function checkTablesExist(array $tableNames): void
    {
        $tablesInDatabase = $this->databaseInformation->tables();
        foreach ($tableNames as $tableName) {
            if (!in_array($tableName, $tablesInDatabase)) {
                throw new ConfigurationFile('Configuration contains table which does not exist in the database: ' . $tableName);
            }
        }
    }

    /**
     * @param array $tablesConfiguration An array of table names as keys with their corresponding configurations as values
     * @return void
     * @throws Exceptions\ConfigurationFile
     */
    private function columnsExist(array $tablesConfiguration): void
    {
        foreach ($tablesConfiguration as $table => $configuration) {
            if (isset($configuration['columns'])) {
                $tableColumns = $this->databaseInformation->columns($table);
                foreach (array_keys($configuration['columns']) as $columnName) {
                    if (!in_array($columnName, $tableColumns)) {
                        throw new ConfigurationFile('Configuration contains column which does not exist in the table: ' . $table . '.' . $columnName);
                    }
                }
            }
        }
    }
}
