<?php
declare(strict_types=1);

namespace Tests\ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Information;

class InformationTest extends DatabaseTestCase
{
    public function testItReturnsArrayOfTables(): void
    {
        $info = (new Information($this->pdo))->tables();

        $this->assertEquals([], $info);
    }
}
