<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Validator;
use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationException;

class ValidatorTest extends DatabaseTestCase
{
    /**
     * @return void
     */
    public function testItThrowsExceptionWhenTableDoesNotExistInDatabase(): void
    {
        $this->expectException(ConfigurationException::class);

        (new Validator($this->pdo))->validateConfiguration(['example_table' => []]);
    }

    /**
     * @return void
     */
    public function testItThrowsExceptionWhenColumnDoesNotExistInTable(): void
    {
        $this->pdo->exec('CREATE TABLE example_table (example_column VARCHAR(17))');
        $this->expectException(ConfigurationException::class);

        (new Validator($this->pdo))->validateConfiguration(['example_table' => ['columns' => ['fake_column' => '']]]);
    }
}
