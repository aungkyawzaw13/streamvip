<?php
session_start();

// လမ်းကြောင်းကို အတိအကျ ပြန်ပြင်ခြင်း
// __DIR__ က လက်ရှိ folder (vipbuy) ကို ပြောတာဖြစ်ပြီး 
// .. က အပြင်ဘက် (sp) folder ကို တစ်ဆင့် ထွက်လိုက်တာ ဖြစ်ပါတယ်
include(__DIR__ . "/../include/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    // config.php ထဲမှာပါတဲ့ $conn variable ရှိမရှိ စစ်ဆေးခြင်း
    if (!isset($conn)) {
        die("Database connection variable (\$conn) ရှာမတွေ့ပါ။ config.php ကို စစ်ဆေးပါ။");
    }

    $user_id = $_SESSION['user_id'];
    $user_phone = $_POST['user_phone'];
    $vip_id = $_POST['vip_id'];
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    $tran_id = $_POST['tran_id'];

    $sql = "INSERT INTO vip_requests (user_id, user_phone, vip_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isisss", $user_id, $user_phone, $vip_id, $amount, $method, $tran_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('တင်သွင်းမှု အောင်မြင်ပါသည်။ Admin အတည်ပြုချက်ကို စောင့်ဆိုင်းပေးပါ။');
            window.location.href='../vip'; 
        </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>