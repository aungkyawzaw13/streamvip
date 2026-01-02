<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./vipbuy/style.css">
    <style>
       .content p {
    line-height: 1.8;
    font-size: 14px;
    text-align: justify;
    color: #e0e0e0;
}
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <i class="fas fa-chevron-left" onclick="history.back()"></i>
        <span>Company Profile</span>
    </div>
    
     <div class="content">
            <p>
                <strong>STREAM VIP (အတိုကောက် SV)</strong> သည် အင်္ဂလန်နိုင်ငံ၊ လန်ဒန်တွင် ရုံးစိုက်သော ရုပ်ရှင်ထုတ်လုပ်ရေး ကုမ္ပဏီတစ်ခုဖြစ်သည်။ ၎င်းသည် ကမ္ဘာ့ထိပ်တန်း ရုပ်ရှင်နှင့် ရုပ်မြင်သံကြား ဖျော်ဖြေရေး ကုမ္ပဏီများထဲမှ တစ်ခုဖြစ်ပြီး လက်ရှိတွင် နိုင်ငံတကာ နာမည်ကြီး Sony Pictures Entertainment နှင့် ချိတ်ဆက်ဆောင်ရွက်လျက် ရှိသည်။ ကြွယ်ဝသောထုတ်လုပ်မှု အတွေ့အကြုံ၊ ခိုင်မာသောအရင်းအမြစ်ပေါင်းစည်းမှုစွမ်းရည်များ နှင့် ခေတ်မီနည်းပညာဆိုင်ရာဆန်းသစ်တီထွင်မှုများဖြင့် SP သည် ကမ္ဘာလုံးဆိုင်ရာပရိသတ်များထံ အရည်အသွေးမြင့်၊ ချီးကျူးဖွယ်ရာရုပ်ရှင်များနှင့် မာလ်တီမီဒီယာအကြောင်းအရာများကို တင်ဆက်ရန် ကတိကဝတ်ပြုထားပါသည်။
            </p>
        </div>
   
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>
