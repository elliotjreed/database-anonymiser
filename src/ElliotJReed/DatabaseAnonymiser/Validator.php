<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;

class Validator
{
    /**
     * Validator constructor.
     * @param DatabaseInformation $databaseInformation
     */
    public function __construct(private DatabaseInformation $databaseInformation)
    {
    }

    /**
     * @param array $tablesConfiguration An array of table names as keys with their corresponding configurations as values
     * @return void
     * @throws Exceptions\UnsupportedDatabase
     * @throws ConfigurationFile
     */
    public function validateConfiguration(array $tablesConfiguration): void
    {
        $invalidTables = $this->invalidTables($tablesConfiguration);

        if (!empty($invalidTables)) {
            throw new ConfigurationFile('Configuration contains tables and / or columns which do not exist: ' . \implode(', ', $invalidTables));
        }
    }

    /**
     * @param array $tablesConfiguration An array of table names as keys with their corresponding configurations as values
     * @return array An array of tables and / or columns which do not exist in the database
     * @throws Exceptions\UnsupportedDatabase
     */
    private function invalidTables(array $tablesConfiguration): array
    {
        $tablesInDatabase = $this->databaseInformation->tables();
        $notInDatabase = [];
        foreach ($tablesConfiguration as $tableName => $configuration) {
            if (!$this->tableInDatabase($tableName, $tablesInDatabase)) {
                $notInDatabase[] = $tableName;
            } elseif (isset($configuration['columns'])) {
                $columnsNotInTable = $this->columnsNotInTable($tableName, $configuration['columns']);
                if (!empty($columnsNotInTable)) {
                    $notInDatabase[] = \implode(', ', $columnsNotInTable);
                }
            }
        }

        return $notInDatabase;
    }

    /**
     * @param string $table The database table to check
     * @param array $tablesInDatabase An array of tables which exist in the database
     * @return bool
     */
    private function tableInDatabase(string $table, array $tablesInDatabase): bool
    {
        return \in_array($table, $tablesInDatabase, true);
    }

    /**
     * @param string $table The database table
     * @param array $columns The columns to check whether or not they exists in the table
     * @return array An array of columns which do not exist in the table
     * @throws Exceptions\UnsupportedDatabase
     */
    private function columnsNotInTable(string $table, array $columns): array
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
