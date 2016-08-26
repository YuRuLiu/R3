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

$sqlInsertUser = "INSERT INTO `user`(`userName`)
                  VALUES ('$userName')";

$sqlSelectUser = "SELECT `userName`
                  FROM `user`
                  WHERE `userName` = '$userName'";
$resSelectUser = $db -> select($sqlSelectUser);

if ($userName != "" && $resSelectUser[0]['userName'] != $userName ) {
    $resInsertUser = $db -> insert($sqlInsertUser);
}

if ($resInsertUser == true) {
    $success = array(
        "result" => "true",
        "username" => $userName
    );

    echo json_encode($success);
}

if ($resSelectUser[0]['userName'] == $userName ) {
    $fail = array(
        "result" => "false",
        "message" => "username repeat"
    );

    echo json_encode($fail);
}
