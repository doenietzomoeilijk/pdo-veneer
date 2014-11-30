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
        $query->from("myTable");
        $this->assertEquals("SELECT * FROM myTable", (string) $query);

        // From with alias.
        $query->reset()
            ->from("myTable", "m");
        $this->assertEquals("SELECT * FROM myTable AS m", (string) $query);

        // Multiple FROMs in one go.
        $query->reset()
            ->from(["myTable", "myothertable"]);
        $this->assertEquals("SELECT * FROM myTable, myothertable", (string) $query);

        $query->reset()
            ->from(["m" => "myTable", "o" => "myothertable"]);
        $this->assertEquals("SELECT * FROM myTable AS m, myothertable AS o", (string) $query);
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

        $query->from("myTable")
            ->where("1");
        $this->assertEquals(
            "SELECT * FROM myTable WHERE (1)",
            (string) $query
        );

        $query->where("myCol IS NOT NULL");
        $this->assertEquals(
            "SELECT * FROM myTable WHERE (1) AND (myCol IS NOT NULL)",
            (string) $query
        );

        $query->where("myCol = ?", [1]);
        $this->assertEquals(
            "SELECT * FROM myTable WHERE (1) AND (myCol IS NOT NULL) AND (myCol = ?)",
            (string) $query
        );

        $query->reset()
            ->from("myTable")
            ->where("myCol = :color", ["color" => "blue"]);
        $this->assertEquals(
            "SELECT * FROM myTable WHERE (myCol = :color)",
            (string) $query
        );
    }

    public function testGroup()
    {
        $query = new Querybuilder;

        $query->from("myTable")
            ->group("myCol");
        $this->assertEquals(
            "SELECT * FROM myTable GROUP BY myCol",
            (string) $query
        );

        $query->group("myOtherCol");
        $this->assertEquals(
            "SELECT * FROM myTable GROUP BY myCol, myOtherCol",
            (string) $query
        );

        $query->group(["col1 ASC", "col2"]);
        $this->assertEquals(
            "SELECT * FROM myTable GROUP BY myCol, myOtherCol, col1 ASC, col2",
            (string) $query
        );
    }

    public function testLimit()
    {
        $query = new Querybuilder;

        $query->from("myTable")
            ->limit(10);
        $this->assertEquals(
            "SELECT * FROM myTable LIMIT 10",
            (string) $query
        );

        $query->limit([10, 10]);
        $this->assertEquals(
            "SELECT * FROM myTable LIMIT 10 OFFSET 10",
            (string) $query
        );

        $query->limit("10, 10");
        $this->assertEquals(
            "SELECT * FROM myTable LIMIT 10, 10",
            (string) $query
        );

        // Reset the limit
        $query->limit(null);
        $this->assertEquals(
            "SELECT * FROM myTable",
            (string) $query
        );
    }
}
