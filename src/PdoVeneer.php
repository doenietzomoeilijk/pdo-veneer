<?php
/**
 * PDO Veneer
 *
 * @copyright
 * @author Max Roeleveld
 */

namespace Dnzm;

/**
 * Class PdoVeneer
 */
class PdoVeneer
{
    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Reset the query builder.
     *
     * @return PdoVeneer $this
     */
    public function reset()
    {
        return $this;
    }

    /**
     * Insert data into a table.
     *
     * Data can be specified as:
     * * An associative array containing column => value pairs;
     * * An object containing column->value properties;
     * * An array containing one or more arrays or object to insert >1 rows.
     *
     * @param string $table
     * @param array|object $data
     * @return void
     */
    public function insert($table, $data)
    {
        // function body
    }

    /**
     * Update a table with new data, optinally limiting the targets.
     *
     * @param string $table
     * @param array|object $data
     * @param string|array $where
     * @return void
     */
    public function update($table, $data, $where = null)
    {
        // function body
    }

    /**
     * Insert data, with an ON DUPLICATE KEY UPDATE clause.
     *
     * @param string $table
     * @param array|object $data
     * @return void
     */
    public function insertOrUpdate($table, $data)
    {
        // function body
    }
}
