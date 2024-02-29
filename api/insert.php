<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

require_once('./database.php');
$tableName = 'history';
$path = './database.db';

if ($_POST['previous'] && $_POST['operator'] && $_POST['current'] ) {
    $previous = $_POST['previous'];
    $operator = $_POST['operator'];
    $current = $_POST['current'];
    insert($path, 'INSERT INTO "' . $tableName . '" (previous, operator, current) VALUES ("' . $previous . '", "' . $operator . '", "' . $current . '");');
}

