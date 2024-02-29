<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

require_once('./database.php');
$tableName = 'history';
$path = './database.db';

if ($_POST['id']) {
    $id = intval($_POST['id']);
    deleteEntry($path, 'DELETE FROM ' . $tableName . ' WHERE id = ' . $id . ';');
}