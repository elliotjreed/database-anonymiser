<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;

class Validator
{
    private DatabaseInformation $databaseInformation;

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
        $tablesNotInDatabase = [];

        foreach ($tableNames as $tableName) {
            if (!\in_array($tableName, $tablesInDatabase, true)) {
                $tablesNotInDatabase[] = $tableName;
            }
        }

        if (!empty($tablesNotInDatabase)) {
            throw new ConfigurationFile('Configuration contains tables which do not exist in the database: ' . \implode(', ', $tablesNotInDatabase));
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
        $columnsNotInTable = [];
        foreach ($tablesConfiguration as $table => $configuration) {
            if (isset($configuration['columns'])) {
                $columnsNotInTable[] = \implode(', ', $this->columnsExistInTable($table, $configuration['columns']));
            }
        }

        if (!empty($columnsNotInTable)) {
            throw new ConfigurationFile('Configuration contains columns which do not exist: ' . \implode(', ', $columnsNotInTable));
        }
    }

    /**
     * @param $table
     * @param $columns
     * @throws ConfigurationFile
     * @throws Exceptions\UnsupportedDatabase
     */
    private function columnsExistInTable(string $table, array $columns): array
    {
        $columnsInDatabaseTable = $this->databaseInformation->columns($table);
        $columnsNotInTable = [];
        foreach (\array_keys($columns) as $columnInConfiguration) {
            if (!\in_array($columnInConfiguration, $columnsInDatabaseTable, true)) {
                $columnsNotInTable[] = $table . '.' . $columnInConfiguration;
            }
        }

        return $columnsNotInTable;
    }
}
