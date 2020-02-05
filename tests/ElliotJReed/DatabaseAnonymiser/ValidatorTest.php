<?php

declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Validator;
use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;

final class ValidatorTest extends DatabaseTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->pdo->exec('CREATE TABLE table_which_exists (column_which_exists VARCHAR(17))');
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE table_which_exists');
    }

    /**
     * @return void
     */
    public function testItReturnsAnArrayOfTableWhichDoNotExistInDatabase(): void
    {
        $this->expectException(ConfigurationFile::class);
        $this->expectExceptionMessage('Configuration contains tables and / or columns which do not exist: fake_table');

        (new Validator(new DatabaseInformation($this->pdo)))->validateConfiguration(['fake_table' => [], 'table_which_exists' => []]);
    }

    /**
     * @return void
     */
    public function testItThrowsExceptionWhenColumnDoesNotExistInTable(): void
    {
        $this->expectException(ConfigurationFile::class);
        $this->expectExceptionMessage('Configuration contains tables and / or columns which do not exist: table_which_exists.fake_column');

        (new Validator(new DatabaseInformation($this->pdo)))->validateConfiguration(['table_which_exists' => ['columns' => ['column_which_exists' => '', 'fake_column' => '', 'fake_column_2' => '']]]);
    }
}
