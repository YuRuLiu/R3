<?php
require_once("Database.php");

$db = new Database();
$userName = $_GET['username'];
$transId = $_GET['transid'];
$type = $_GET['type'];
$amount = $_GET['amount'];

//判斷transid是否重複
$sqlSelectTransId = "SELECT `userName`, `transId`
                     FROM `detail`
                     WHERE (`userName` = '$userName' AND `transId` = '$transId')";
$resSelectTransId = $db -> select($sqlSelectTransId);

$TransIdRepeat = array(
    "result" => "false",
    "message" => "Transid repeat"
);

if ($resSelectTransId[0]['transId'] != "") {
    echo json_encode($TransIdRepeat);
    exit;
}

//轉入
if ($type == "IN") {
    $sqlSelectUser = "SELECT `userName`, `balance`
                      FROM `user`
                      WHERE `userName` = '$userName'";
    $resSelectUser = $db -> select($sqlSelectUser);
    $name = $resSelectUser[0]['userName'];
    $balance = $resSelectUser[0]['balance'];

    $balance = $balance + $amount;

    $sqlInsertTransfer = "INSERT INTO `detail`(`userName`, `transId`, `moneyIn`, `balance`)
                          VALUES ('$name', '$transId', $amount, '$balance')";
    $resInsertTransfer = $db -> insert($sqlInsertTransfer);

    $sqlUpdateBalance = "UPDATE `user`
                         SET `balance`= '$balance'
                         WHERE `userName` = '$userName'";
    $resUpdateBalance = $db -> update($sqlUpdateBalance);
}

//轉出
if ($type == "OUT") {
    //判斷餘額不足
    $sqlSelectUser = "SELECT `balance`
                      FROM `user`
                      WHERE `userName` = '$userName'";
    $resSelectUser = $db -> select($sqlSelectUser);
    $balance = $resSelectUser[0]['balance'];

    $InsufficientBalance = array(
        "result" => "false",
        "message" => "Insufficient balance"
    );

    if ($balance < $amount) {
        echo json_encode($InsufficientBalance);
        exit;
    }

    try{
        $db->transaction();
        $sqlSelectUser = "SELECT `userName`, `balance`
                          FROM `user`
                          WHERE `userName` = '$userName' LOCK IN SHARE MODE";
        $resSelectUser = $db -> select($sqlSelectUser);
        $name = $resSelectUser[0]['userName'];
        $balance = $resSelectUser[0]['balance'];

        if ($balance >= $amount) {
            $balance = $balance - $amount;
            $sqlInsertTransfer = "INSERT INTO `detail`(`userName`, `transId`, `moneyOut`, `balance`)
                                  VALUES ('$name', '$transId', $amount, '$balance')";
            $resInsertTransfer = $db -> insert($sqlInsertTransfer);

            $sqlUpdateBalance = "UPDATE `user`
                                 SET `balance`= '$balance'
                                 WHERE `userName` = '$userName'";
            $resUpdateBalance = $db -> update($sqlUpdateBalance);
        }

        $db->commit();
    } catch (Exception $e) {
        $this->rollback();
    }
}

//判斷轉帳是否成功，並顯示餘額
$sqlSelectUser = "SELECT `userName`, `balance`
                  FROM `user`
                  WHERE `userName` = '$userName'";
$resSelectUser = $db -> select($sqlSelectUser);
$balance = $resSelectUser[0]['balance'];

$success = array(
    "result" => "true",
    "balance" => $balance,
    "message" => "transfer is success"
);

if ($resInsertTransfer == true && $resUpdateBalance == true) {
    echo json_encode($success);
}