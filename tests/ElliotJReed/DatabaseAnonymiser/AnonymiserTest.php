<?php

declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Anonymiser;
use ElliotJReed\DatabaseAnonymiser\DatabaseConfiguration;
use ElliotJReed\DatabaseAnonymiser\DatabaseInformation;
use ElliotJReed\DatabaseAnonymiser\Validator;
use PDO;

final class AnonymiserTest extends DatabaseTestCase
{
    private Anonymiser $anonymiser;

    public function setUp(): void
    {
        parent::setUp();
        $this->anonymiser = new Anonymiser($this->pdo, new DatabaseConfiguration($this->pdo), new Validator(new DatabaseInformation($this->pdo)));
    }

    public function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE example_table');
    }

    public function testItAnonymisesString(): void
    {
        $this->pdo->exec("
          CREATE TABLE example_table (example_column VARCHAR(17));
          INSERT INTO example_table (example_column) VALUES ('original string')
        ");
        $configuration = [
            'example_table' => [
                'columns' => [
                    'example_column' => 'anonymised string'
                ]
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column FROM example_table')->fetch(PDO::FETCH_COLUMN);

        $this->assertSame('anonymised string', $result);
    }

    public function testItAnonymisesMultipleColumns(): void
    {
        $this->pdo->exec("
          CREATE TABLE example_table (example_column VARCHAR(17), second_example_column VARCHAR(24));
          INSERT INTO example_table (example_column, second_example_column) VALUES ('original string', 'second original string')
        ");
        $configuration = [
            'example_table' => [
                'columns' => [
                    'example_column' => 'anonymised string',
                    'second_example_column' => 'second anonymised string'
                ]
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column, second_example_column FROM example_table')->fetch();

        $this->assertSame('anonymised string', $result['example_column']);
        $this->assertSame('second anonymised string', $result['second_example_column']);
    }

    public function testItRemovesNumberOfRowsInTable(): void
    {
        $this->pdo->exec('
          CREATE TABLE example_table (example_column INT(1));
          INSERT INTO example_table (example_column) VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9)
        ');
        $configuration = [
            'example_table' => [
                'remove' => 4
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column FROM example_table')->fetch(PDO::FETCH_COLUMN);

        $this->assertEquals(5, $result);
    }

    public function testItRemovesNumberOfRowsInTableRetainingMostRecent(): void
    {
        $this->pdo->exec('
          CREATE TABLE example_table (example_column INT(1));
          INSERT INTO example_table (example_column) VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9)
        ');
        $configuration = [
            'example_table' => [
                'remove' => 4
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column FROM example_table')->fetchAll(PDO::FETCH_COLUMN);

        $this->assertEquals([5, 6, 7, 8, 9], $result);
    }

    public function testItRemovesNumberOfRowsAndReplacesValues(): void
    {
        $this->pdo->exec("
          CREATE TABLE example_table (example_column CHAR(1), second_example_column INT(1));
          INSERT INTO example_table (example_column, second_example_column) VALUES ('a', 1), ('b', 2), ('c', 3), ('d', 4), ('e', 5)
        ");
        $configuration = [
            'example_table' => [
                'remove' => 3,
                'columns' => [
                    'example_column' => 'x',
                    'second_example_column' => 9
                ]
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column, second_example_column FROM example_table')->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals([['example_column' => 'x', 'second_example_column' => 9], ['example_column' => 'x', 'second_example_column' => 9]], $result);
    }

    public function testItTruncatesTable(): void
    {
        $this->pdo->exec('
          CREATE TABLE example_table (example_column INT(1));
          INSERT INTO example_table (example_column) VALUES (1), (2), (3)
        ');
        $configuration = [
            'example_table' => [
                'truncate' => true
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT * FROM example_table')->fetchAll();

        $this->assertSame([], $result);
    }

    public function testItRetainsNumberOfRowsRetainingMostRecent(): void
    {
        $this->pdo->exec('
          CREATE TABLE example_table (example_column INT(1));
          INSERT INTO example_table (example_column) VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9)
        ');
        $configuration = [
            'example_table' => [
                'retain' => 3
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column FROM example_table')->fetchAll(PDO::FETCH_COLUMN);

        $this->assertEquals([7, 8, 9], $result);
    }

    public function testItDoesNothingWhenNumberOfRowsInTableIsLessThanRetainNumber(): void
    {
        $this->pdo->exec('
          CREATE TABLE example_table (example_column INT(1));
          INSERT INTO example_table (example_column) VALUES (1), (2), (3)
        ');
        $configuration = [
            'example_table' => [
                'retain' => 4
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column FROM example_table')->fetchAll(PDO::FETCH_COLUMN);

        $this->assertEquals([1, 2, 3], $result);
    }

    public function testItRetainsNumberOfRowsAndReplacesValues(): void
    {
        $this->pdo->exec("
          CREATE TABLE example_table (example_column CHAR(1), second_example_column INT(1));
          INSERT INTO example_table (example_column, second_example_column) VALUES ('a', 1), ('b', 2), ('c', 3), ('d', 4), ('e', 5)
        ");
        $configuration = [
            'example_table' => [
                'retain' => 2,
                'columns' => [
                    'example_column' => 'x',
                    'second_example_column' => 9
                ]
            ]
        ];
        $this->anonymiser->anonymise($configuration);
        $result = $this->pdo->query('SELECT example_column, second_example_column FROM example_table')->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals([['example_column' => 'x', 'second_example_column' => 9], ['example_column' => 'x', 'second_example_column' => 9]], $result);
    }
}
