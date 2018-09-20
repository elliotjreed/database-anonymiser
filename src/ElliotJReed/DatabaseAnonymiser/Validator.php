<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationException;
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
     * @throws ConfigurationException
     * @throws Exceptions\UnsupportedDatabaseException
     */
    public function validateConfiguration(array $tablesConfiguration): void
    {
        if (!$this->tablesExist(array_keys($tablesConfiguration))) {
            throw new ConfigurationException('Configuration contains table(s) which do not exist in the database');
        }
        if (!$this->columnsExist($tablesConfiguration)) {
            throw new ConfigurationException('Configuration contains columns(s) which do not exist in the database');
        }
    }

    /**
     * @param array $tableNames An array of table names
     * @return bool
     * @throws Exceptions\UnsupportedDatabaseException
     */
    private function tablesExist(array $tableNames): bool
    {
        return !array_diff($tableNames, $this->databaseInformation->tables());
    }

    /**
     * @param array $tablesConfiguration An array of table names as keys with their corresponding configurations as values
     * @return bool Returns false if the columns specified do not exist, true if they all do or the configuration does not specify any columns
     */
    private function columnsExist(array $tablesConfiguration): bool
    {
        foreach ($tablesConfiguration as $table => $configuration) {
            if (!isset($configuration['columns'])) {
                return true;
            }
            $tableColumns = $this->databaseInformation->columns($table);
            foreach (array_keys($configuration['columns']) as $columnName) {
                if (!in_array($columnName, $tableColumns)) {
                    return false;
                }
            }
        }

        return true;
    }
}
