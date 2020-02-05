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
        $this->pdo->exec('CREATE TABLE example_table (example_column VARCHAR(17))');
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE example_table');
    }

    /**
     * @return void
     */
    public function testItReturnsAnArrayOfTableWhichDoNotExistInDatabase(): void
    {
        $this->expectException(ConfigurationFile::class);
        $this->expectExceptionMessage('Configuration contains tables which do not exist in the database: fake_table');

        (new Validator(new DatabaseInformation($this->pdo)))->validateConfiguration(['fake_table' => []]);
    }

    /**
     * @return void
     */
    public function testItThrowsExceptionWhenColumnDoesNotExistInTable(): void
    {
        $this->expectException(ConfigurationFile::class);
        $this->expectExceptionMessage('Configuration contains columns which do not exist: example_table.fake_column');

        (new Validator(new DatabaseInformation($this->pdo)))->validateConfiguration(['example_table' => ['columns' => ['fake_column' => '']]]);
    }
}
