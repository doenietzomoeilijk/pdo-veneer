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
     * @return void
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
     * Actually add the join.
     *
     * @param string $type
     * @param string|array $toAdd
     * @param string $joinOn
     * @return void
     */
    private function addGenericJoin($type, $toAdd, $joinOn, $alias = null)
    {
        if (is_array($toAdd)) {
            // This is going to bomb if you feed it the wrong kind of array.
            // Protip: don't feed it the wrong kind of array.
            foreach ($toAdd as $key => $value) {
                $this->addGenericJoin($type, $value, $joinOn, $key);
            }
        } else {
            if ($alias !== null && !is_int($alias)) {
                $toAdd .= " AS $alias";
            }

            $this->parts["join"][] = [$type, $toAdd, $joinOn];
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
     * @param string|array $table
     * @param string $on
     * @return Querybuilder $this
     */
    public function join($table, $on)
    {
        $this->addGenericJoin("", $table, $on);
        return $this;
    }

    /**
     * @param string|array $table
     * @param string $on
     * @return Querybuilder $this
     */
    public function innerJoin($table, $on)
    {
        $this->addGenericJoin("inner", $table, $on);
        return $this;
    }

    /**
     * @param string|array $table
     * @param string $on
     * @return Querybuilder $this
     */
    public function outerJoin($table, $on)
    {
        $this->addGenericJoin("outer", $table, $on);
        return $this;
    }

    /**
     * @return PdoVeneer\Querybuilder $this
     */
    public function where($where)
    {
        // @todo Maybe add a way to add "OR" wheres, too?
        $this->parts["where"][] = "($where)";
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

        if (!empty($this->parts["join"])) {
            foreach ($this->parts["join"] as $join) {
                list($type, $joinClause, $joinOn) = $join;
                $type = $type ? strtoupper($type) : "";
                $query[] = trim("$type JOIN $joinClause ON $joinOn");
            }
        }

        if (!empty($this->parts["where"])) {

            $query[] = "WHERE " . implode(" AND ", $this->parts["where"]);

        }

        return implode(" ", $query);
    }
}
