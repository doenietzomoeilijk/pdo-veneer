<?php
/**
 * The query builder.
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

    /**
     * Named parameters go here.
     * @type array $parameters
     */
    protected $parameters;

    /**
     * Keeps track of how many parameters sans name have been added.
     * @type integer $parameterCount
     */
    protected $parameterCount;

    public function __construct()
    {
        $this->reset();
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
            "orWhere" => [],
            "group" => [],
            "having" => [],
            "order" => [],
            "limit" => [],
        ];

        $this->parameters = [];
        $this->parametercount = 0;

        return $this;
    }

    /**
     * Does the heavy adding-lifting.
     *
     * Yes, you've guessed it right, you can abuse my laziness to have a query
     * with "limit 10 AS yourmom".
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
     * Overwrite a generic.
     *
     * @param string $type one of the keys in the $parts array
     * @param string $toAdd string of one field to set
     * @param string $alias
     * @return void
     */
    private function setGeneric($type, $toSet, $alias = null)
    {
        if ($alias !== null && !is_int($alias)) {
            $toSet .= " AS $alias";
        }

        $this->parts[$type] = $toSet;
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
     * @return PdoVeneer\Querybuilder $this
     */
    public function select($select = null, $alias = null)
    {
        if ($select !== null) {
            $this->addGeneric("select", $select, $alias);
        }

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
     * You can either feed it a straight string, or a string containing
     * parameters and an array containing the values for those parameters.
     *
     * When feeding it an array of parameters, you can specify an associative
     * array if you use named parameters, or a numeric array if you use simple
     * questions marks.
     *
     * @param string $where
     * @param array $params
     * @return PdoVeneer\Querybuilder $this
     */
    public function where($where, array $params = null)
    {
        // @todo: actually use the parameters when running the query!
        if ($params !== null) {
            $this->parameters = array_merge($this->parameters, $params);
        }

        $this->parts["where"][] = "($where)";

        return $this;
    }

    /**
     * @param string|array $groups
     * @return PdoVeneer\Querybuilder $this
     */
    public function group($groups)
    {
        $this->addGeneric("group", $groups);
        return $this;
    }

    /**
     * Can be specified as one of:
     *
     * * limit(10) -- to set a single, integer limit
     * * limit([10, 10]) -- to set a limit, offset
     * * limit("10, 10") -- to set the same limit as a string
     *
     * @param string|integer|array $limit
     * @return PdoVeneer\Querybuilder $this
     */
    public function limit($limit)
    {
        // assume that any array is a limit + offset
        if (is_array($limit)) {
            list($limit, $offset) = $limit;
            $limit = "$limit OFFSET $offset";
        }

        $this->setGeneric("limit", $limit);
        return $this;
    }

    /**
     * Returns the parameters that were set.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
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

        if (!empty($this->parts["group"])) {
            $query[] = "GROUP BY " . implode(", ", $this->parts["group"]);
        }

        if (!empty($this->parts["limit"])) {
            $query[] = "LIMIT {$this->parts['limit']}";
        }

        return implode(" ", $query);
    }
}
