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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw - VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #121212; color: #ffffff; display: flex; justify-content: center; }
        
        .app-container { width: 100%; max-width: 450px; min-height: 100vh; background: #121212; position: relative; }

        /* Header Bar Area */
        .header-bar {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #1a1a1d;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-bar .back-icon {
            font-size: 20px;
            color: #ffcc00;
            cursor: pointer;
            margin-right: 20px;
        }
        .header-bar h2 { font-size: 18px; font-weight: 600; }

        /* Balance Card */
        .balance-card {
            background: linear-gradient(135deg, #2c2c34 0%, #1a1a1d 100%);
            margin: 20px;
            padding: 25px;
            border-radius: 20px;
            border: 1px solid #333;
            text-align: center;
        }
        .balance-card p { color: #888; font-size: 14px; margin-bottom: 8px; }
        .balance-card h1 { color: #ffcc00; font-size: 32px; font-weight: bold; }

        /* Form Styling */
        .form-section { padding: 0 20px 30px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; color: #888; margin-bottom: 8px; margin-left: 5px; }
        .input-group input, .input-group select {
            width: 100%;
            background: #1a1a1d;
            border: 1px solid #333;
            padding: 15px;
            border-radius: 12px;
            color: white;
            outline: none;
            transition: 0.3s;
        }
        .input-group input:focus { border-color: #ffcc00; }

        .note-text { font-size: 12px; color: #ff4444; margin-bottom: 20px; padding-left: 5px; }

        .withdraw-btn {
            width: 100%;
            background: #ffcc00;
            color: #000;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }
        .withdraw-btn:active { transform: scale(0.98); }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header-bar">
        <i class="fas fa-arrow-left back-icon" onclick="history.back()"></i>
        <h2>ငွေထုတ်ယူရန်</h2>
    </div>

    <div class="balance-card">
        <p>လက်ရှိအိတ်ကပ်ထဲကငွေ</p>
        <h1><?= number_format($user['balance']) ?> <small style="font-size: 14px;">MMK</small></h1>
    </div>

    <div class="form-section">
        <form id="withdrawForm">
            <div class="input-group">
                <label><i class="fas fa-money-bill-wave me-2"></i> ထုတ်ယူမည့်ပမာဏ</label>
                <input type="number" name="amount" placeholder="အနည်းဆုံး ၅၀၀၀ ကျပ်" required min="5000">
            </div>

            <div class="input-group">
                <label><i class="fas fa-university me-2"></i> ငွေထုတ်မည့်နည်းလမ်း</label>
                <select name="method">
                    <option value="Kpay">Kpay</option>
                    <option value="WavePay">WavePay</option>
                    <option value="CBPay">CB Pay</option>
                </select>
            </div>

            <div class="input-group">
                <label><i class="fas fa-id-card me-2"></i> အကောင့်/ဖုန်းနံပါတ်</label>
                <input type="text" name="acc_number" placeholder="09xxxxxxxxx" required>
            </div>

            <div class="input-group">
                <label><i class="fas fa-user me-2"></i> အကောင့်အမည်</label>
                <input type="text" name="acc_name" placeholder="အမည်ရိုက်ထည့်ပါ" required>
            </div>

            <p class="note-text">* ငွေထုတ်ယူမှုသည် ၂၄ နာရီအတွင်း အောင်မြင်ပါမည်။</p>

            <button type="submit" class="withdraw-btn">အတည်ပြုသည်</button>
        </form>
    </div>
</div>
<script>
document.getElementById("withdrawForm").onsubmit = function(e){
    e.preventDefault();

    Swal.fire({
        title:'အတည်ပြုပါ',
        text:'ငွေထုတ်ယူမည်လား?',
        icon:'question',
        showCancelButton:true,
        confirmButtonText:'OK'
    }).then(res=>{
        if(res.isConfirmed){
            Swal.fire({title:'Processing...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
            fetch("/sp/Withdraws/submit_withdraw.php",{method:"POST",body:new FormData(this)})
            .then(res=>res.text())
            .then(r=>{
                if(r.trim()=="success"){
                    Swal.fire('Success','တောင်းဆိုပြီးပါပြီ','success')
                    .then(()=>location.href="withdraw_history.php");
                }else{
                    Swal.fire('Error',r,'error');
                }
            });
        }
    });
};
</script>
</body>
</html>