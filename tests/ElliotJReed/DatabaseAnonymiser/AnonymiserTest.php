<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Anonymiser;
use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationException;
use PDO;

class AnonymiserTest extends DatabaseTestCase
{
    public function testItThrowsExceptionWhenTableDoesNotExistInDatabase(): void
    {
        $this->expectException(ConfigurationException::class);
        $confirguation = [
            'example_table' => [
                'example_row' => 'anonymised string'
            ]
        ];
        (new Anonymiser($this->pdo))->anonymise($confirguation);
    }

    public function testItAnonymisesString()
    {
        $this->pdo->exec('CREATE TABLE example_table (example_row VARCHAR(17))');
        $this->pdo->exec('INSERT INTO example_table (example_row) VALUES ("original string")');
        $confirguation = [
            'example_table' => [
                'example_row' => 'anonymised string'
            ]
        ];
        (new Anonymiser($this->pdo))->anonymise($confirguation);
        $result = $this->pdo->query('SELECT example_row FROM example_table')->fetch(PDO::FETCH_COLUMN);

        $this->assertEquals('anonymised string', $result);
    }
}
