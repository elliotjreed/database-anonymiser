<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;

class Validator
{
    private $databaseInformation;

    /**
     * Validator constructor.
     * @param DatabaseInformation $databaseInformation
     */
    public function __construct(DatabaseInformation $databaseInformation)
    {
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
        $this->checkTablesExist(\array_keys($tablesConfiguration));
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
            if (!\in_array($tableName, $tablesInDatabase, true)) {
                throw new ConfigurationFile('Configuration contains table which does not exist in the database: ' . $tableName);
            }
        }
    }

    /**
     * @param array $tablesConfiguration An array of table names as keys with their corresponding configurations as values
     * @return void
     * @throws Exceptions\ConfigurationFile
     * @throws Exceptions\UnsupportedDatabase
     */
    private function columnsExist(array $tablesConfiguration): void
    {
        foreach ($tablesConfiguration as $table => $configuration) {
            if (isset($configuration['columns'])) {
                $this->columnsExistInTable($table, $configuration['columns']);
            }
        }
    }

    /**
     * @param $tableInConfiguration
     * @param $columnsInConfiguration
     * @throws ConfigurationFile
     * @throws Exceptions\UnsupportedDatabase
     */
    private function columnsExistInTable(string $tableInConfiguration, array $columnsInConfiguration): void
    {
        $columnsInDatabaseTable = $this->databaseInformation->columns($tableInConfiguration);
        foreach (\array_keys($columnsInConfiguration) as $columnInConfiguration) {
            if (!\in_array($columnInConfiguration, $columnsInDatabaseTable, true)) {
                throw new ConfigurationFile('Configuration contains column which does not exist in the table: ' . $tableInConfiguration . '.' . $columnInConfiguration);
            }
        }
    }
}
