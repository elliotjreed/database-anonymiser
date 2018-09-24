<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Validator;
use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationFile;

class ValidatorTest extends SqliteTestCase
{
    /**
     * @return void
     */
    public function testItThrowsExceptionWhenTableDoesNotExistInDatabase(): void
    {
        $this->expectException(ConfigurationFile::class);

        (new Validator($this->pdo, new DatabaseInformation($this->pdo)))->validateConfiguration(['example_table' => []]);
    }

    /**
     * @return void
     */
    public function testItThrowsExceptionWhenColumnDoesNotExistInTable(): void
    {
        $this->pdo->exec('CREATE TABLE example_table (example_column VARCHAR(17))');
        $this->expectException(ConfigurationFile::class);

        (new Validator($this->pdo, new DatabaseInformation($this->pdo)))->validateConfiguration(['example_table' => ['columns' => ['fake_column' => '']]]);
    }
}
