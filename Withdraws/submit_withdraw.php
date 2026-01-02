<?php
session_start();
include(__DIR__ . "/../include/config.php");

if(!isset($_SESSION['user_id'])){
    echo "Login required"; exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($_POST['amount']);
$method = $_POST['method'];
$acc_no = $_POST['acc_number'];
$acc_name = $_POST['acc_name'];

if($amount < 5000){
    echo "အနည်းဆုံး 5000 MMK"; exit;
}

// balance check
$stmt = $conn->prepare("SELECT balance FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user || $user['balance'] < $amount){
    echo "လက်ကျန်ငွေ မလုံလောက်ပါ"; exit;
}

$conn->begin_transaction();
try{
    $new_balance = $user['balance'] - $amount;

    $stmt = $conn->prepare("UPDATE users SET balance=? WHERE id=?");
    $stmt->bind_param("di",$new_balance,$user_id);
    $stmt->execute();

    $stmt = $conn->prepare("
        INSERT INTO withdraw_requests
        (user_id,amount,payment_method,account_number,account_name,status)
        VALUES (?,?,?,?,?,'pending')
    ");
    $stmt->bind_param("idsss",$user_id,$amount,$method,$acc_no,$acc_name);
    $stmt->execute();

    $conn->commit();
    echo "success";
}catch(Exception $e){
    $conn->rollback();
    echo "System error";
}
