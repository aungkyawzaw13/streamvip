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
    <link rel="stylesheet" href="./user/profile.css">
</head>
<body>
    <div class="app-container">
        <header class="top-nav">
            
            <img src="https://spfilmapp.com/xml/static/images/logo-en-US.png?t=1" class="top-logo" alt="Logo">
            <div class="chat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
        </header>
    <div class="profile">
        <div class="top-info">
            <img src="https://spfilm.vip/xml/static/head/head_12.png?t=2" class="avatar">

            <h3 style="margin:5px 0;"><?= htmlspecialchars($user["phone"]) ?></h3>
            <span style="color:#aaa;"><?= htmlspecialchars($user["vip_level"]) ?></span>
        </div>
        <!--<div class="balance-card">
            <p class="balance-label">အဓိကပိုက်ဆံအိတ် (MMK)</p>
            <p class="balance-amount">1000</p>
        </div>-->
        <div class="stats-grid">
            <div class="stat-box"><p>အဓိကပိုက်ဆံအိတ် (MMK)</p><h4><?= htmlspecialchars($user["balance"]) ?></h4></div>
            <div class="stat-box"><p>ဒီနေ့ဝကော်မရှင်ပိုက်ဆံအိတ်(MMK)</p><h4>0.00</h4></div>
            <div class="stat-box"><p>စုစုပေါင်းဝင်ငွေ</p><h4>0.00</h4></div>
            <div class="stat-box"><p>လွှဲပြောင်း Rebate</p><h4>0.00</h4></div>
        </div>
        <br>
        <div class="stats-grid">
            <div class="stat-box"><p>မနေ့ကဝင်ငွေ</p><h4>0.00</h4></div>
            <div class="stat-box"><p>ဒီနေ့ဝင်ငွေ</p><h4>0.00</h4></div>
            <div class="stat-box"><p>ဒီလဝင်ငွေ</p><h4>0.00</h4></div>
            <div class="stat-box"><p>စုစုပေါင်းဝင်ငွေ</p><h4>0.00</h4></div>
        </div>
        <div class="list-menu">
            <div class="menu-link" onclick="location.href='./info'"> ကိုယ်ပိုင်အချက်အလက်များ <span>❯</span></div>
            <div class="menu-link">📑 အလုပ်မှတ်တမ်းများ <span>❯</span></div>
            <div class="menu-link">📢 နေ့စဉ်ကြေညာချက် <span>❯</span></div>
        </div>
    </div>
    <?php
        include('./footer.php');
        ?>
        </div>
</body>
</html>