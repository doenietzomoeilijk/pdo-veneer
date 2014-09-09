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
            ->from(["mytable", "myothertable"]);
        $this->assertEquals("SELECT * FROM mytable, myothertable", (string) $query);

        $query->reset()
            ->from([
                "m" => "mytable",
                "o" => "myothertable",
            ]);
        $this->assertEquals("SELECT * FROM mytable AS m, myothertable AS o", (string) $query);
    }

    public function testJoins()
    {
        $query = new Querybuilder;

        $query->from("table")
            ->join("othertable", "table.col = othertable.col");
        $this->assertEquals(
            "SELECT * FROM table JOIN othertable ON table.col = othertable.col",
            (string) $query
        );

        $query->reset()
            ->from("table")
            ->innerJoin("othertable", "table.col = othertable.col");
        $this->assertEquals(
            "SELECT * FROM table INNER JOIN othertable ON table.col = othertable.col",
            (string) $query
        );

        $query->reset()
            ->from("table")
            ->outerJoin(["o" => "othertable"], "table.col = o.col");
        $this->assertEquals(
            "SELECT * FROM table OUTER JOIN othertable AS o ON table.col = o.col",
            (string) $query
        );

        $query->join(["y" => "yetanothertable"], "y.col = o.col");
        $this->assertEquals(
            "SELECT * FROM table OUTER JOIN othertable AS o ON table.col = o.col JOIN yetanothertable AS y ON y.col = o.col",
            (string) $query
        );
    }

    public function testWhere()
    {
        $query = new Querybuilder;

        $query->from("mytable")
            ->where("1");
        $this->assertEquals(
            "SELECT * FROM mytable WHERE (1)",
            (string) $query
        );

        $query->where("mycol IS NOT NULL");
        $this->assertEquals(
            "SELECT * FROM mytable WHERE (1) AND (mycol IS NOT NULL)",
            (string) $query
        );
    }
}
