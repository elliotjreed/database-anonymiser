<?php

declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\DatabaseConfiguration;

final class MysqlDatabaseConfigurationTest extends DatabaseTestCase
{
    public function testItTurnsOffForeignKeyChecks(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=ON');
        (new DatabaseConfiguration($this->pdo))->disableForeignKeyChecks();

        $query = $this->pdo->query('SHOW LOCAL VARIABLES LIKE "foreign_key_checks"');

        $this->assertSame('OFF', $query->fetch()['Value']);
    }
}
