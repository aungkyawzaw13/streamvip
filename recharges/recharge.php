<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}
include(__DIR__ . "/../include/config.php");

$uid = $_SESSION["user_id"];

// လက်ကျန်ငွေကို ပြန်ခေါ်ခြင်း
$stmt = mysqli_prepare($conn, "SELECT balance FROM users WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$current_balance = number_format($user['balance'] ?? 0);
?>
<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ငွေဖြည့်ရန်</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Pyidaungsu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./user/wallet.css"> <style>
        .recharge-container { padding: 20px; color: white; }
        .amount-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 20px; }
        .amount-card { background: #333; padding: 15px 5px; text-align: center; border-radius: 8px; cursor: pointer; border: 1px solid transparent; }
        .amount-card.active { border-color: #ffc107; background: #444; color: #ffc107; }
        .input-group { margin-top: 25px; }
        .input-group input { width: 100%; padding: 12px; background: #222; border: 1px solid #444; border-radius: 5px; color: white; font-size: 16px; margin-top: 8px; box-sizing: border-box;}
        .payment-methods { margin-top: 25px; }
        .payment-item { display: flex; align-items: center; background: #333; padding: 12px; border-radius: 8px; margin-bottom: 10px; cursor: pointer; }
        .submit-btn { width: 100%; background: #ffc107; color: #000; border: none; padding: 15px; border-radius: 5px; font-weight: bold; font-size: 16px; margin-top: 30px; cursor: pointer; }
        .yellow-text { color: #ffc107; }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <span class="back-btn" onclick="history.back()">&#10094;</span>
        <span class="title">ငွေဖြည့်သွင်းရန်</span>
    </div>

    <div class="recharge-container">
        <p>လက်ရှိလက်ကျန်ငွေ: <span class="yellow-text"><?= $current_balance ?> ကျပ်</span></p>

        <div class="input-group">
            <label>ငွေဖြည့်မည့်ပမာဏ (ကျပ်)</label>
            <input type="number" id="recharge-amount" placeholder="အနည်းဆုံး ၅,၀၀၀">
        </div>

        <div class="amount-grid">
            <div class="amount-card" onclick="setAmount(5000)">5,000</div>
            <div class="amount-card" onclick="setAmount(10000)">10,000</div>
            <div class="amount-card" onclick="setAmount(30000)">30,000</div>
            <div class="amount-card" onclick="setAmount(50000)">50,000</div>
            <div class="amount-card" onclick="setAmount(100000)">100,000</div>
            <div class="amount-card" onclick="setAmount(200000)">200,000</div>
        </div>

        <div class="payment-methods">
            <label>ငွေပေးချေမှုစနစ် ရွေးချယ်ပါ</label>
            <div class="payment-item" onclick="selectPayment('kpay')">
                <span>KPay (KBZ Bank)</span>
            </div>
            <div class="payment-item" onclick="selectPayment('wave')">
                <span>Wave Pay</span>
            </div>
        </div>

        <button class="submit-btn" onclick="submitRecharge()">ချက်ချင်းဖြည့်မည်</button>
    </div>
</div>

<script>
    let selectedAmount = 0;
    let selectedMethod = '';

    function setAmount(amount) {
        document.getElementById('recharge-amount').value = amount;
        selectedAmount = amount;
        // UI Active class change
        document.querySelectorAll('.amount-card').forEach(card => card.classList.remove('active'));
        event.target.classList.add('active');
    }

    function selectPayment(method) {
        selectedMethod = method;
        document.querySelectorAll('.payment-item').forEach(item => item.style.border = "none");
        event.currentTarget.style.border = "1px solid #ffc107";
    }

    function submitRecharge() {
        const amount = document.getElementById('recharge-amount').value;
        if(amount < 5000) {
            Swal.fire('အမှား', 'အနည်းဆုံး ၅,၀၀၀ ကျပ် ဖြည့်ရပါမည်', 'error');
            return;
        }
        if(!selectedMethod) {
            Swal.fire('အမှား', 'ငွေပေးချေမှုစနစ် ရွေးချယ်ပါ', 'warning');
            return;
        }

        // ဤနေရာတွင် API ဆီသို့ Data ပို့ရန်
        console.log("Recharging:", amount, "via", selectedMethod);
        Swal.fire('အောင်မြင်ပါသည်', 'ခေတ္တစောင့်ဆိုင်းပေးပါ...', 'success');
        // နောက်စာမျက်နှာ (Screenshot တင်သည့်နေရာ) သို့ လွှဲပေးရန်
         window.location.href = '/sp/recharges/payment_confirm.php?amount=' + amount + '&method=' + selectedMethod;
    }
</script>

</body>
</html>