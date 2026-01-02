<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}
include(__DIR__ . "/../include/config.php");

$amount = isset($_GET['amount']) ? (int)$_GET['amount'] : 0;
$method = isset($_GET['method']) ? $_GET['method'] : '';

$payment_info = [
    'kpay' => ['name' => 'U Aung Aung', 'phone' => '09123456789', 'label' => 'KPay'],
    'wave' => ['name' => 'Daw Mya Mya', 'phone' => '09987654321', 'label' => 'Wave Pay']
];

$selected = $payment_info[$method] ?? null;

if(!$selected || $amount <= 0) {
    echo "Invalid Request";
    exit;
}
?>
<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ငွေလွှဲအတည်ပြုရန်</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/recharges/wallet.css"> <style>
        .confirm-box { padding: 20px; color: white; text-align: center; }
        .payment-details { background: #333; padding: 20px; border-radius: 12px; margin-top: 15px; text-align: left; }
        .copy-btn { background: #ffc107; color: black; border: none; padding: 5px 10px; border-radius: 4px; float: right; cursor: pointer; font-size: 12px; }
        .upload-area { border: 2px dashed #555; padding: 30px; margin-top: 20px; border-radius: 10px; cursor: pointer; position: relative; min-height: 150px; }
        #preview-img { width: 100%; max-width: 250px; display: none; margin: 10px auto; border-radius: 8px; border: 1px solid #ffc107; }
        .submit-btn { width: 100%; background: #ffc107; color: #000; border: none; padding: 15px; border-radius: 5px; font-weight: bold; margin-top: 25px; cursor: pointer; }
        .yellow-text { color: #ffc107; }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <span class="back-btn" onclick="history.back()">&#10094;</span>
        <span class="title">ငွေလွှဲအထောက်အထားတင်ရန်</span>
    </div>

    <div class="confirm-box">
        <h3 class="yellow-text"><?= number_format($amount) ?> ကျပ်</h3>
        <p>အောက်ပါအကောင့်သို့ တိကျစွာ ငွေလွှဲပေးပါ</p>

        <div class="payment-details">
            <p>အမျိုးအစား: <strong><?= $selected['label'] ?></strong></p>
            <p>နာမည်: <strong><?= $selected['name'] ?></strong></p>
            <p style="margin-top: 10px;">အကောင့်နံပါတ်: <br>
                <strong id="acc-no" style="font-size: 20px;"><?= $selected['phone'] ?></strong>
                <button class="copy-btn" onclick="copyText()">Copy</button>
            </p>
        </div>

        <form id="rechargeForm">
            <input type="hidden" name="amount" value="<?= $amount ?>">
            <input type="hidden" name="method" value="<?= $method ?>">
            
            <div class="upload-area" onclick="document.getElementById('file-input').click()">
                <p id="upload-text">ငွေလွှဲဖြတ်ပိုင်း (Screenshot) တင်ရန် နှိပ်ပါ</p>
                <img id="preview-img" src="#" alt="Preview">
                <input type="file" name="screenshot" id="file-input" hidden accept="image/*" onchange="previewImage(this)">
            </div>

            <button type="submit" class="submit-btn">တင်ပြမည်</button>
        </form>
    </div>
</div>

<script>
// ၁။ စာသား Copy ကူးရန် Function
function copyText() {
    const text = document.getElementById('acc-no').innerText;
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({ icon: 'success', title: 'ကော်ပီကူးပြီးပါပြီ', showConfirmButton: false, timer: 1000 });
    });
}

// ၂။ ပုံ Preview ကြည့်ရန် Function
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('preview-img');
            img.src = e.target.result;
            img.style.display = 'block';
            document.getElementById('upload-text').style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// ၃။ Form Submit လုပ်ရန် Function
document.getElementById('rechargeForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    if(!document.getElementById('file-input').files[0]) {
        Swal.fire('သတိပြုရန်', 'ငွေလွှဲဖြတ်ပိုင်း Screenshot တင်ပေးပါ', 'warning');
        return;
    }

    Swal.fire({
        title: 'တင်ပြနေပါသည်...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('./submit_recharge.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire('အောင်မြင်ပါသည်', 'စစ်ဆေးပြီးပါက ဖုန်းထဲသို့ ငွေဝင်လာပါလိမ့်မည်', 'success')
            .then(() => { window.location.href = './wallet'; });
        } else {
            Swal.fire('အမှား', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('အမှား', 'Server နှင့် ချိတ်ဆက်မှု မအောင်မြင်ပါ', 'error');
    });
};
</script>
</body>
</html>