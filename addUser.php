<?php
require_once("Database.php");

$userName = $_GET['username'];

$db = new Database();

$sqlInsertUser = "INSERT INTO `user`(`userName`)
                  VALUES ('$userName')";

$sqlSelectUser = "SELECT `userName`
                  FROM `user`
                  WHERE `userName` = '$userName'";
$resSelectUser = $db -> select($sqlSelectUser);

if ($userName != "" && $resSelectUser[0]['userName'] != $userName ) {
    $resInsertUser = $db -> insert($sqlInsertUser);
}

$success = array(
    "result" => "true",
    "username" => $userName
);

$fail = array(
    "result" => "false",
    "message" => "username repeat"
);

if ($resInsertUser == true) {
    echo json_encode($success);
}

if ($resSelectUser[0]['userName'] == $userName ) {
    echo json_encode($fail);
}