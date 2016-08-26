<?php
require_once("Database.php");
header("Content-Type:text/html; charset=utf-8");

$userName = $_GET['username'];
$transId = $_GET['transid'];
$type = $_GET['type'];
$amount = $_GET['amount'];

$db = new Database();

if ($userName == null || $transId == null || $type == null || $amount == null) {
    $parameterFail = array(
        "result" => "false",
        "message" => "quantity of parameter is wrong"
    );

    echo json_encode($parameterFail);
    exit;
}

$sqlSelectUser = "SELECT `userName`, `balance`
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

if (!is_numeric($transId) || $transId < 0) {
    $transidFail = array(
        "result" => "false",
        "message" => "transid should be a positive integer"
    );

    echo json_encode($transidFail);
    exit;
}

if (!is_numeric($amount) || $amount < 0) {
    $amountFail = array(
        "result" => "false",
        "message" => "amount should be a positive integer"
    );

    echo json_encode($amountFail);
    exit;
}

if ($type != "IN" && $type != "OUT") {
    $typeFail = array(
        "result" => "false",
        "message" => "type shoule be IN or OUT"
    );

    echo json_encode($typeFail);
    exit;
}

//判斷transid是否重複
$sqlSelectTransId = "SELECT `userName`, `transId`
                     FROM `detail`
                     WHERE (`userName` = '$userName' AND `transId` = '$transId')";
$resSelectTransId = $db -> select($sqlSelectTransId);


if ($resSelectTransId[0]['transId'] != "") {
    $TransIdRepeat = array(
        "result" => "false",
        "message" => "Transid repeat"
    );

    echo json_encode($TransIdRepeat);
    exit;
}

//判斷餘額不足
$balance = $resSelectUser[0]['balance'];
if ($balance < $amount) {
    $InsufficientBalance = array(
        "result" => "false",
        "message" => "Insufficient balance"
    );

    echo json_encode($InsufficientBalance);
    exit;
}

//轉入
if ($type === "IN") {
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
if ($type === "OUT") {
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

        $fail = array(
            "result" => "false",
            "message" => "transfer is fail"
        );

        echo json_encode($fail);
    }
}

//判斷轉帳是否成功
if ($resInsertTransfer == true && $resUpdateBalance == true) {
    $success = array(
        "result" => "true",
        "balance" => $balance,
        "message" => "transfer is success"
    );

    echo json_encode($success);
}
