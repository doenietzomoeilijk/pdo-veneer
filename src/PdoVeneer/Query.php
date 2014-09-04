<?php
/**
 * The query builder
 *
 * @copyright
 * @author Max Roeleveld
 */

namespace Dnzm\PdoVeneer;

/**
 * Class Query
 */
class Query
{
    /**
     * An array of query parts.
     *
     * @var array $queryParts
     */
    protected $queryParts = array();

    /**
     * @return PdoVeneer\Query $this
     */
    public function select()
    {
        // function body
        return $this;
    }

    /**
     * @return PdoVeneer\Query $this
     */
    public function from()
    {
        // function body
        return $this;
    }

    /**
     * @return PdoVeneer\Query $this
     */
    public function where()
    {
        // function body
        return $this;
    }

    /**
     * @return PdoVeneer\Query $this
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
