<?php
session_start();

// Database Connection လမ်းကြောင်းကို သေချာစစ်ပါ။ 
// အကယ်၍ config.php က ဖိုင်ချင်းကပ်လျက်ဆိုရင် include("config.php"); လို့ပဲ ရေးပါ။
$config_path = __DIR__ . "/../include/config.php";
if (file_exists($config_path)) {
    include($config_path);
} else {
    // config ဖိုင်ရှာမတွေ့ပါက လက်ရှိ folder ထဲ ရှာကြည့်ရန်
    include("config.php"); 
}

if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}

$vip_id = isset($_GET['vip_id']) ? intval($_GET['vip_id']) : 0;

$sql = "SELECT * FROM vip WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vip_id);
$stmt->execute();
$result = $stmt->get_result();
$vip = $result->fetch_assoc();

if(!$vip){
    die("<h2 style='color:white; text-align:center; margin-top:50px;'>VIP အချက်အလက် ရှာမတွေ့ပါ။ <a href='index.php'>ပြန်သွားရန်</a></h2>");
}
?>
<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - VIP Upgrade</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS များကို ဤနေရာတွင် တိုက်ရိုက်ထည့်ထားခြင်းက 'File Not Found' error ကို ကာကွယ်ပေးပါသည် */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
        body { background-color: #121212; color: white; display: flex; justify-content: center; }
        .app-container { width: 100%; max-width: 450px; min-height: 100vh; padding: 20px; }
        .header { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; cursor: pointer; }
        .info-card { background: #24242b; padding: 20px; border-radius: 15px; margin-bottom: 20px; border-left: 5px solid #ffcc00; }
        .info-card h3 { color: #ffcc00; margin-bottom: 5px; }
        .payment-methods { display: flex; gap: 15px; margin-bottom: 25px; }
        .method { flex: 1; background: #24242b; padding: 15px; border-radius: 12px; text-align: center; cursor: pointer; border: 2px solid transparent; transition: 0.3s; }
        .method.active { border-color: #ffcc00; background: #2c2c34; }
        .method img { width: 45px; height: 45px; border-radius: 8px; margin-bottom: 8px; }
        .copy-box { background: #1a1a1d; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border: 1px dashed #444; }
        .copy-btn { background: #ffcc00; border: none; padding: 6px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; color: #888; margin-bottom: 8px; }
        .input-group input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #444; background: #1a1a1d; color: white; outline: none; }
        .submit-btn { width: 100%; padding: 15px; background: #ffcc00; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="header" onclick="history.back()">
            <i class="fas fa-arrow-left"></i>
            <span>ငွေပေးချေမှု</span>
        </div>

        <div class="info-card">
            <h3><?= htmlspecialchars($vip['title']) ?></h3>
            <p>ပေးချေရမည့်ပမာဏ: <b style="color:white;">MMK <?= number_format($vip['price']) ?></b></p>
        </div>

        <label style="font-size: 13px; color: #888; display: block; margin-bottom: 10px;">Payment နည်းလမ်းရွေးချယ်ပါ</label>
        <div class="payment-methods">
            <div class="method active" onclick="selectMethod('Kpay', '09428466521')">
                <img src="https://manga.one7777.net/assets/png/kpay.png" alt="KPay">
                <span>KPay</span>
            </div>
            <div class="method" onclick="selectMethod('Wave', '09428466521')">
                <img src="https://manga.one7777.net/assets/png/wavepay.png" alt="WavePay">
                <span>WavePay</span>
            </div>
        </div>

        <div class="copy-box">
            <div>
                <p style="font-size: 11px; color: #888; margin-bottom: 5px;">ငွေလွှဲရမည့်ဖုန်းနံပါတ်</p>
                <span id="pay_num" style="color: #ffcc00; font-size: 18px; font-weight: bold;">09428466521</span>
            </div>
            <button class="copy-btn" onclick="copyNum()">Copy</button>
        </div>

        <form action="submit_order.php" method="POST" id="payForm">
            <input type="hidden" name="vip_id" value="<?= $vip['id'] ?>">
            <input type="hidden" name="amount" value="<?= $vip['price'] ?>">
            <input type="hidden" name="method" id="selected_method" value="Kpay">

            <div class="input-group">
                <label>ပြေစာ (Transaction ID) နောက်ဆုံး ၆ လုံး</label>
                <input type="number" name="tran_id" id="tran_id" placeholder="123456" required>
            </div>
            <button type="submit" class="submit-btn">အတည်ပြုသည်</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function selectMethod(name, num) {
            document.querySelectorAll('.method').forEach(m => m.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('pay_num').innerText = num;
            document.getElementById('selected_method').value = name;
        }

        function copyNum() {
            const num = document.getElementById('pay_num').innerText;
            navigator.clipboard.writeText(num);
            Swal.fire({ title: 'Copy ကူးပြီးပါပြီ', icon: 'success', timer: 1000, showConfirmButton: false });
        }

        document.getElementById('payForm').onsubmit = function() {
            const tid = document.getElementById('tran_id').value;
            if(tid.length < 6) {
                Swal.fire('အမှား', 'Transaction ID နောက်ဆုံး ၆ လုံးအပြည့်ထည့်ပါ', 'error');
                return false;
            }
        };
    </script>
</body>
</html>