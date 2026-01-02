<?php
session_start();
header('Content-Type: application/json');
include(__DIR__ . "/../include/config.php");

if(!isset($_SESSION["user_id"])){
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$uid = $_SESSION["user_id"];

// Adjust table and column names to match your database
$sql = "SELECT amount, status, created_at FROM withdraw_requests WHERE user_id = ? ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$history = [];
while($row = mysqli_fetch_assoc($result)){
    $history[] = [
        "description" => "ငွေထုတ်ယူခြင်း", // Withdrawal
        "amount" => number_format($row['amount'], 2),
        "status" => $row['status'],
        "created_at" => date("d-m-Y H:i", strtotime($row['created_at'])),
        "type" => "withdraw"
    ];
}

echo json_encode($history);