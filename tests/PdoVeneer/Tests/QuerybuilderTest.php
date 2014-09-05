<?php

namespace PdoVeneer\Tests;

use \Dnzm\PdoVeneer\Querybuilder;

class QuerybuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnTypes ()
    {
        $query = new QueryBuilder;

        $this->assertInstanceOf("Dnzm\PdoVeneer\Querybuilder", $query);
        $this->assertInternalType("string", (string) $query);
    }
}
