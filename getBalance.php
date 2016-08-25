<?php
require_once("Database.php");
header("Content-Type:text/html; charset=utf-8");

$db = new Database();
$userName = $_GET['username'];

$sqlSelectBalance = "SELECT `userName`, `balance`
                     FROM `user`
                     WHERE `userName` = '$userName'";
$resSelectBalance = $db -> select($sqlSelectBalance);


$success = array(
    "result" => "true",
    "balance" => $resSelectBalance[0]['balance']
);

$fail = array(
    "result" => "false",
    "message" => "this user does not exist"
);

if ($resSelectBalance[0]['userName'] != "") {
    echo json_encode($success);
}

if ($resSelectBalance[0]['userName'] == "") {
    echo json_encode($fail);
}
