<?php
require_once("Database.php");
header("Content-Type:text/html; charset=utf-8");

$db = new Database();
$userName = $_GET['username'];
$transId = $_GET['transid'];

if ($userName == null || $transId == null) {
    $parameterFail = array(
        "result" => "false",
        "message" => "quantity of parameter is wrong"
    );

    echo json_encode($parameterFail);
    exit;
}

$sqlSelectUser = "SELECT `userName`
                  FROM `user`
                  WHERE `userName` = '$userName'";
$resSelectUser = $db -> select($sqlSelectUser);

if ($resSelectUser[0]['userName'] == "") {
    $userFail = array(
        "result" => "false",
        "message" => "this user does not exist"
    );

    echo json_encode($userFail);
    exit;
}

$sqlSelectTransId = "SELECT `transId`
                     FROM `detail`
                     WHERE (`userName` = '$userName' AND `transId` = '$transId')";
$resSelectTransId = $db -> select($sqlSelectTransId);

if ($resSelectTransId[0]['transId'] != "") {
    $success = array(
        "result" => "true",
        "message" => "transfer is success"
    );

    echo json_encode($success);
}

if ($resSelectTransId[0]['transId'] == "") {
    $fail = array(
        "result" => "false",
        "message" => "transfer is fail"
    );

    echo json_encode($fail);
}