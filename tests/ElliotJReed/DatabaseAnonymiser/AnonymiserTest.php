<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Anonymiser;
use ElliotJReed\DatabaseAnonymiser\Exceptions\ConfigurationException;
use PDO;

class AnonymiserTest extends DatabaseTestCase
{
    /* @var PDO */
    private $db;

    public function setUp(): void
    {
        $this->db = $this->sqlite()->getConnection();
    }

    public function testItThrowsExceptionWhenTableDoesNotExistInDatabase(): void
    {
        $this->expectException(ConfigurationException::class);

        (new Anonymiser($this->db))->anonymise(['example_table' => []]);
    }

    public function testItAnonymisesString(): void
    {
        $this->db->exec('CREATE TABLE example_table (example_row VARCHAR(17))');
        $this->db->exec('INSERT INTO example_table (example_row) VALUES ("original string")');
        $confirguation = [
            'example_table' => [
                'example_row' => 'anonymised string'
            ]
        ];
        (new Anonymiser($this->db))->anonymise($confirguation);
        $result = $this->db->query('SELECT example_row FROM example_table')->fetch(PDO::FETCH_COLUMN);

        $this->assertEquals('anonymised string', $result);
    }
}
