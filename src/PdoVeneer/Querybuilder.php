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
     * @var array $parts
     */
    protected $parts;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Does the heavy adding-lifting. Yes, you've guessed it right, you can
     * abuse my laziness to have a query with "limit 10 AS yourmom".o
     *
     * @param string $type one of the keys in the $parts array
     * @param string|array $toAdd string of one or array of alias => field
     * @param string $alias
     */
    private function addGeneric($type, $toAdd, $alias = null)
    {
        if (is_array($toAdd)) {
            foreach ($toAdd as $key => $value) {
                $this->addGeneric($type, $value, $key);
            }
        } else {
            if ($alias !== null && !is_int($alias)) {
                 $toAdd .= " AS $alias";
            }

            $this->parts[$type][] = $toAdd;
        }
    }

    /**
     * Reset the query.
     *
     * @return Querybuilder $this
     */
    public function reset()
    {
        $this->parts = [
            "select" => [],
            "from" => [],
            "flags" => [],
            "join" => [],
            "where" => [],
            "group" => [],
            "having" => [],
            "order" => [],
        ];

        return $this;
    }

    /**
     * @return PdoVeneer\Querybuilder $this
     */
    public function select($select, $alias = null)
    {
        $this->addGeneric("select", $select, $alias);
        return $this;
    }

    /**
     * @param string|array $table name or array of alias => table pairs.
     * @param string $alias
     * @return PdoVeneer\Querybuilder $this
     */
    public function from($table, $alias = null)
    {
        $this->addGeneric("from", $table, $alias);
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
    public function groupBy($groups)
    {
        return $this;
    }

    /**
     * @return PdoVeneer\Querybuilder $this
     */
    public function limit($groups)
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
        $query = ["SELECT"];

        if (!empty($this->parts["select"])) {
            $query[] = implode(", ", (array) $this->parts["select"]);
        } else {
            // You really shouldn't do this, but who am I to cramp your style...
            $query[] = "*";
        }

        if (!empty($this->parts["from"])) {
            $query[] = "FROM " . implode(", ", $this->parts['from']);
        }

        return implode(" ", $query);
    }
}
