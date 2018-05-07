<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationException;
use PDO;

class Anonymiser
{
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function anonymise(array $tables): void
    {
        if (!$this->checkTablesExist(array_keys($tables))) {
            throw new ConfigurationException('Configuration contains table(s) which do not exist in the database');
        }
        foreach ($tables as $tableName => $rows) {
            foreach ($rows as $row => $newValue) {
                $query = $this->db->prepare('UPDATE `' . $tableName . '` SET `' . $row . '` = ?');
                $query->execute([$newValue]);
            }
        }
    }

    private function checkTablesExist(array $tablesConfiguration): bool
    {
        $tables = (new Information($this->db))->tables();

        return !array_diff($tablesConfiguration, $tables);
    }
}
