<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}
include ('./include/config.php');

$uid = $_SESSION["user_id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT phone, vip_level, balance FROM users WHERE id=? LIMIT 1"
);
mysqli_stmt_bind_param($stmt,"i",$uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile UI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pyidaungsu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./user/info.css">
</head>
<body>

<div class="app-container">
    <div class="header">
        <span class="back-btn" onclick="location.href='./profile'">&#10094;</span>
        <span class="title">ကိုယ်ပိုင်အချက်အလက်များ</span>
    </div>

    <div class="profile-list">
        <div class="list-item">
            <span class="label">ဦး ခေါင်းပုံ</span>
            <div class="right-section">
                <img src="https://spfilm.vip/xml/static/head/head_12.png?t=2" alt="Profile" class="avatar">
                <span class="arrow">&#10095;</span>
            </div>
        </div>

        <div class="list-item">
            <span class="label">မိုဘိုင်းနံပါတ်</span>
            <span class="value"><?= htmlspecialchars($user["phone"]) ?></span>
        </div>

        <div class="list-item clickable" onclick="showAlert('အသေးစိတ်အချက်အလက်များ')">
            <span class="label">အသေးစိတ်အချက်အလက်များ</span>
            <div class="right-section">
                <span class="action-text">ချိန်ညှိချက်များကိုနှိပ်ပါ</span>
                <span class="arrow">&#10095;</span>
            </div>
        </div>

        <div class="list-item clickable" onclick="showAlert('ဘဏ်ကဒ်')">
            <span class="label">ဘဏ်ကဒ်</span>
            <div class="right-section">
                <span class="action-text">ချိန်ညှိချက်များကိုနှိပ်ပါ</span>
                <span class="arrow">&#10095;</span>
            </div>
        </div>

        <div class="list-item clickable" onclick="showAlert('Login စကားဝှက်')">
            <span class="label">Login စကားဝှက်</span>
            <div class="right-section">
                <span class="action-text">ချိန်ညှိချက်များကိုနှိပ်ပါ</span>
                <span class="arrow">&#10095;</span>
            </div>
        </div>

        <div class="list-item clickable" onclick="showAlert('Fund Password')">
            <span class="label">Fund Password</span>
            <div class="right-section">
                <span class="action-text">ချိန်ညှိချက်များကိုနှိပ်ပါ</span>
                <span class="arrow">&#10095;</span>
            </div>
        </div>

        <div class="list-item">
            <span class="label light-text">cache ကိုဗလာ</span>
            <span class="arrow">&#10095;</span>
        </div>
    </div>

    <div class="logout-section">
        <button class="logout-btn" onclick="logout()">ထွက်ရန် login</button>
    </div>
    
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./user/script.js"></script>
</body>
</html>