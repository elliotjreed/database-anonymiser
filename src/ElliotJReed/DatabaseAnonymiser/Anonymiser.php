<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationException;
use PDO;

class Anonymiser
{
    private $pdo;
    private $validator;

    /**
     * Anonymiser constructor.
     * @param PDO $pdo
     * @param Validator $validator
     */
    public function __construct(PDO $pdo, Validator $validator)
    {
        $this->pdo = $pdo;
        $this->validator = $validator;
    }

    /**
     * @param array $tables An array of table names with their corresponding configurations
     * @throws ConfigurationException
     * @throws Exceptions\UnsupportedDatabaseException
     * @return void
     */
    public function anonymise(array $tables): void
    {
        $this->validator->validateConfiguration($tables);
        foreach ($tables as $tableName => $configuration) {
            $this->processTable($tableName, $configuration);
        }
    }

    /**
     * @param string $tableName The name of the table to be processed
     * @param array $configuration
     * @return void
     */
    private function processTable(string $tableName, array $configuration): void
    {
        if (isset($configuration['truncate']) && $configuration['truncate'] === true) {
            $this->truncate($tableName);
        } else {
            $this->modifyRows($tableName, $configuration);
        }
    }

    /**
     * @param string $tableName The name of the table to truncate
     * @return void
     */
    private function truncate(string $tableName): void
    {
        $this->pdo->exec('DELETE FROM ' . $tableName);
    }

    /**
     * @param string $tableName
     * @param array $configuration
     */
    private function modifyRows(string $tableName, array $configuration): void
    {
        if (isset($configuration['retain'])) {
            $this->retainColumns($tableName, $configuration['retain']);
        } elseif (isset($configuration['remove'])) {
            $this->removeColumns($tableName, $configuration['remove']);
        }

        if (isset($configuration['columns'])) {
            $this->replaceRowValues($tableName, $configuration['columns']);
        }
    }

    /**
     * @param string $tableName The name of the table to remove columns from
     * @param int $numberOfRowsToRetain The number of rows to retain in the table
     * @return void
     */
    private function retainColumns(string $tableName, int $numberOfRowsToRetain): void
    {
        $query = $this->pdo->query('SELECT COUNT(*) FROM ' . $tableName);
        $totalRows = $query->fetch(PDO::FETCH_COLUMN);

        $rowsToRemove = $totalRows - $numberOfRowsToRetain;

        $this->removeColumns($tableName, $rowsToRemove);
    }

    /**
     * @param string $tableName The name of the table to remove columns from
     * @param int $numberOfRowsToRemove The number of rows to remove from the table
     * @return void
     */
    private function removeColumns(string $tableName, int $numberOfRowsToRemove): void
    {
        $query = $this->pdo->prepare('DELETE FROM `' . $tableName . '` LIMIT :limit');
        $query->bindValue('limit', $numberOfRowsToRemove, PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * @param string $tableName The name of the table to replace column values
     * @param array $columns An array of columns and their corresponding row replacement values
     * @return void
     */
    private function replaceRowValues(string $tableName, array $columns): void
    {
        $replacementParameters = '';
        foreach ($columns as $column => $replacementValue) {
            $replacementParameters .= $column . ' = ?, ';
        }

        $query = $this->pdo->prepare('UPDATE `' . $tableName . '` SET ' . trim($replacementParameters, ', '));
        $query->execute(array_values($columns));
    }
}
