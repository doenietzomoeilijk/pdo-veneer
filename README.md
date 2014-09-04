PDO-Veneer
==========

A thin wrapper around PHP's PDO objects.

```php
<?php
use \Dnzm\PdoVeneer;
use \Dnzm\PdoVeneer\Flags;

$pdo = new PdoVeneer(new \PDO(/* options */));

// Using Flags::FOUND_ROWS will both add SQL_CALC_FOUND_ROWS to the select, and
// run a SELECT FOUND_ROWS() afterwards.
$stmt = $pdo->select(Flags::FOUND_ROWS | Flags::NO_CACHE)
    ->from("table", "alias")
    ->where("foo = ?", 123)
    ->where("bar = ?", "bla")
    ->limit(10)
    ->run();

// Only works immediately afterwards.
$foundRows = $pdo->foundRows();

// Insert a single row.
$pdo->insert("table", array(
    "column1" => 123,
    "column2" => "herpderp",
));

// Insert multiple rows. Only the first row needs column names; actually,
// they'll be ignored for the remaining rows. It's up to you to make sure the order
// is correct.
$pdo->insert("table", array(
    array(
        "column1" => 123,
        "column2" => "herpderp",
    ),
    array(345, "harpdarp"),
    array(567, "foo bar!"),
));

// If you want to issue an INSERT .. ON DUPLICATE KEY UPDATE query, you can do
// that too:
$pdo->insertOrUpdate("table", array(
    "column1" => 123,
    "column2" => "herpderp",
));
```
