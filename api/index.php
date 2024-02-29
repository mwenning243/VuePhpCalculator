<?php
header('Access-Control-Allow-Origin: *');

require_once('./database.php');
$tableName = 'history';
$path = './database.db';
//createTable('./database.db', "
//    $tableName (id integer primary key autoincrement, previous, operator, current);
//");

//insert($path, 'INSERT INTO ' . $tableName . ' (previous, operator, current) VALUES ("2", "-", "4");');

$rows = select($path, "SELECT * FROM $tableName;");
$historyEntries = [];
if (count($rows) > 0) {
    foreach ($rows as $row => $value) {
        array_push($historyEntries, $value);
    }
    echo json_encode($historyEntries);
}
