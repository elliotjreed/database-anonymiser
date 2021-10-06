<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;
use PDO;

class Anonymiser
{
    /**
     * Anonymiser constructor.
     *
     * @param PDO                   $pdo
     * @param DatabaseConfiguration $configuration
     * @param Validator             $validator
     */
    public function __construct(private PDO $pdo, private DatabaseConfiguration $configuration, private Validator $validator)
    {
    }

    /**
     * @param array $tables An array of table names with their corresponding configurations
     *
     * @throws ConfigurationFile
     * @throws Exceptions\UnsupportedDatabase
     */
    public function anonymise(array $tables): void
    {
        $this->configuration->disableForeignKeyChecks();
        $this->validator->validateConfiguration($tables);
        foreach ($tables as $tableName => $configuration) {
            $this->processTable($tableName, $configuration);
        }
    }

    /**
     * @param string $tableName The name of the table to be processed
     */
    private function processTable(string $tableName, array $configuration): void
    {
        if (isset($configuration['truncate']) && true === $configuration['truncate']) {
            $this->truncate($tableName);
        } else {
            $this->modifyRows($tableName, $configuration);
        }
    }

    /**
     * @param string $tableName The name of the table to truncate
     */
    private function truncate(string $tableName): void
    {
        if ('mysql' === $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            $this->pdo->exec('TRUNCATE TABLE ' . $this->safeTableName($tableName));
        } else {
            $this->pdo->exec('DELETE FROM ' . $this->safeTableName($tableName));
        }
    }

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
     * @param string $tableName            The name of the table to remove columns from
     * @param int    $numberOfRowsToRetain The number of rows to retain in the table
     */
    private function retainColumns(string $tableName, int $numberOfRowsToRetain): void
    {
        $query = $this->pdo->query('SELECT COUNT(*) FROM ' . $this->safeTableName($tableName));
        $totalRows = (int) $query->fetch(PDO::FETCH_COLUMN);

        if ($totalRows >= $numberOfRowsToRetain) {
            $rowsToRemove = $totalRows - $numberOfRowsToRetain;
            $this->removeColumns($tableName, $rowsToRemove);
        }
    }

    /**
     * @param string $tableName            The name of the table to remove columns from
     * @param int    $numberOfRowsToRemove The number of rows to remove from the table
     */
    private function removeColumns(string $tableName, int $numberOfRowsToRemove): void
    {
        $query = $this->pdo->prepare('DELETE FROM ' . $this->safeTableName($tableName) . ' LIMIT :limit');
        $query->bindValue('limit', $numberOfRowsToRemove, PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * @param string $tableName The name of the table to replace column values
     * @param array  $columns   An array of columns and their corresponding row replacement values
     */
    private function replaceRowValues(string $tableName, array $columns): void
    {
        $parameters = '';
        foreach ($columns as $column => $replacementValue) {
            $parameters .= $column . ' = ?, ';
        }

        $query = $this->pdo->prepare('UPDATE ' . $this->safeTableName($tableName) . ' SET ' . \trim($parameters, ', '));
        $query->execute(\array_values($columns));
    }

    /**
     * @param string $tableName The name of the table to ensure is safe for use in SQL
     *
     * @return string The safe table name to use in SQL
     */
    private function safeTableName(string $tableName): string
    {
        return '`' . \str_replace('`', '``', $tableName) . '`';
    }
}
