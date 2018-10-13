<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\DatabaseConfiguration;
use PDOException;

class MysqlDatabaseConfigurationTest extends DatabaseTestCase
{
    /**
     * @return void
     */
    public function testItTurnsOffForeignKeyChecks(): void
    {
        $this->pdo->exec(
            'SET FOREIGN_KEY_CHECKS=1;
            CREATE TABLE example_table (example_column INT(1) PRIMARY KEY);
            INSERT INTO example_table (example_column) VALUES (1);
            CREATE TABLE example_second_table (example_column INT(1), ref_column INT(1), FOREIGN KEY(ref_column) REFERENCES example_table(example_column));
            INSERT INTO example_second_table (example_column, ref_column) VALUES (1, 1);'
        );

        (new DatabaseConfiguration($this->pdo))->disableForeignKeyChecks();
        $rowsAffected = $this->pdo->exec('DROP TABLE example_table');

        $this->assertSame(1, $rowsAffected);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $this->pdo->exec(
            'DROP TABLE example_table;
          DROP TABLE example_second_table'
        );
    }

    /**
     * @return void
     */
    public function testItThrowsExceptionByDefaultIfForeignKeyChecksAreEnabled(): void
    {
        $this->expectException(PDOException::class);

        $this->pdo->exec(
            'SET FOREIGN_KEY_CHECKS=1;
            CREATE TABLE example_table (example_column INT(1) PRIMARY KEY);
            INSERT INTO example_table (example_column) VALUES (1);
            CREATE TABLE example_second_table (example_column INT(1), ref_column INT(1), FOREIGN KEY(ref_column) REFERENCES example_table(example_column));
            INSERT INTO example_second_table (example_column, ref_column) VALUES (1, 1);
            DROP TABLE example_table;'
        );
    }
}
