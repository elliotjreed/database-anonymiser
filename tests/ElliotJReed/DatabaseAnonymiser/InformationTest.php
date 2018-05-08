<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Information;

class InformationTest extends DatabaseTestCase
{
    public function testItReturnsArrayOfTablesForSqlite(): void
    {
        $info = (new Information($this->sqlite()->getConnection()))->tables();

        $this->assertEquals([], $info);
    }

    public function testItReturnsArrayOfTablesForMysql(): void
    {
        $info = (new Information($this->mysql()->getConnection()))->tables();

        $this->assertEquals([], $info);
    }

    public function testItReturnsArrayOfTablesForPostgres(): void
    {
        $info = (new Information($this->postgres()->getConnection()))->tables();

        $this->assertEquals([], $info);
    }
}
