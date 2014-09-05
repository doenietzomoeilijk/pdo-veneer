<?php
/**
 * The query builder
 *
 * @copyright
 * @author Max Roeleveld
 */

namespace Dnzm\PdoVeneer;

/**
 * Class Querybuilder
 */
class Querybuilder
{
    /**
     * An array of query parts.
     *
     * @var array $queryParts
     */
    protected $queryParts = array();

    /**
     * @return PdoVeneer\Querybuilder $this
     */
    public function select()
    {
        return $this;
    }

    /**
     * @param string $table name
     * @param string $alias
     * @return PdoVeneer\Querybuilder $this
     */
    public function from($table, $alias = null)
    {
        $part = $table;
        if ($alias !== null) {
            $part .= " AS $alias";
        }

        $this->queryParts["from"] = $part;

        return $this;
    }

    /**
     * @return PdoVeneer\Querybuilder $this
     */
    public function where($where)
    {
        return $this;
    }

    /**
     * @return PdoVeneer\Querybuilder $this
     */
    public function groupBy()
    {
        return $this;
    }

    /**
     * Return the string.
     *
     * @return string
     */
    public function __toString()
    {
        return "";
    }
}
