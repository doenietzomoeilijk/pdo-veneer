<?php

namespace PdoVeneer\Tests;

use \Pdo;
use \Dnzm\PdoVeneer;

class VeneerTest extends \PHPUnit_Framework_TestCase
{
    static private $pdo = null;
    private $conn = null;

    /**
     * For now we test against sqlite.
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO('sqlite::memory:');
            }
        }

        return self::$pdo;
    }

    public function testReturnPdo ()
    {
        $pdo = new PdoVeneer($this->getConnection());
        $this->assertInstanceOf("Dnzm\PdoVeneer", $pdo);
    }
}
