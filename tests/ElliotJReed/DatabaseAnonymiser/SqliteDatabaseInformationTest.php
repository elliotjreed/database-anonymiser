<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;

class SqliteDatabaseInformationTest extends DatabaseTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->pdo->exec('CREATE TABLE example_table (example_column VARCHAR(17), second_example_column VARCHAR(24))');
    }

    /**
     * @return void
     */
    public function testItReturnsArrayOfTables(): void
    {
        $info = (new DatabaseInformation($this->pdo))->tables();

        $this->assertSame(['example_table'], $info);
    }

    /**
     * @return void
     */
    public function testItReturnsArrayOfColumns(): void
    {
        $info = (new DatabaseInformation($this->pdo))->columns('example_table');

        $this->assertSame(['example_column', 'second_example_column'], $info);
    }
}
