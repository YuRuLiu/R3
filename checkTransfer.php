<?php
require_once("Database.php");

$db = new Database();
$userName = $_GET['username'];
$transId = $_GET['transid'];

$sqlSelectTransId = "SELECT `transId`
                     FROM `detail`
                     WHERE (`userName` = '$userName' AND `transId` = '$transId')";
$resSelectTransId = $db -> select($sqlSelectTransId);

$success = array(
    "result" => "true",
    "message" => "transfer is success"
);

$fail = array(
    "result" => "false",
    "message" => "transfer is fail"
);

if ($resSelectTransId[0]['transId'] != "") {
    echo json_encode($success);
}

if ($resSelectTransId[0]['transId'] == "") {
    echo json_encode($fail);
}