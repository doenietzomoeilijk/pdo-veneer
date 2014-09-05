<?php

namespace PdoVeneer\Tests;

use \Dnzm\PdoVeneer\Querybuilder;

class QuerybuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnTypes()
    {
        $query = new QueryBuilder;

        $this->assertInstanceOf("Dnzm\PdoVeneer\Querybuilder", $query);
        $this->assertInternalType("string", (string) $query);
    }

    public function testSelect()
    {
        $query = new Querybuilder;

        // Selecting nothing should result in *.
        $this->assertEquals("SELECT *", (string) $query);

        // Selecting a single column.
        $query->reset()
            ->select("column");
        $this->assertEquals("SELECT column", (string) $query);
    }

    public function testFrom()
    {
        $query = new Querybuilder;

        // Simple from.
        $query->from("mytable");
        $this->assertEquals("SELECT * FROM mytable", (string) $query);

        // From with alias.
        $query->reset()
            ->from("mytable", "m");
        $this->assertEquals("SELECT * FROM mytable AS m", (string) $query);

        // Multiple FROMs in one go.
        $query->reset()
            ->from([
                "mytable",
                "myothertable"
            ]);
        $this->assertEquals("SELECT * FROM mytable, myothertable", (string) $query);
    }
}
