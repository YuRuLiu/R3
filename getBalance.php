<?php
require_once("Database.php");
header("Content-Type:text/html; charset=utf-8");

$userName = $_GET['username'];

$db = new Database();

if ($userName == null) {
    $parameterFail = array(
        "result" => "false",
        "message" => "quantity of parameter is wrong"
    );

    echo json_encode($parameterFail);
    exit;
}

$sqlSelectBalance = "SELECT `userName`, `balance`
                     FROM `user`
                     WHERE `userName` = '$userName'";
$resSelectBalance = $db -> select($sqlSelectBalance);

if ($resSelectBalance[0]['userName'] != "") {
    $success = array(
        "result" => "true",
        "balance" => $resSelectBalance[0]['balance']
    );
    echo json_encode($success);
}

if ($resSelectBalance[0]['userName'] == "") {
    $fail = array(
        "result" => "false",
        "message" => "this user does not exist"
    );
    echo json_encode($fail);
}
