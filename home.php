<?php
session_start();
$conn = new mysqli("127.0.0.1", "root", "", "movie");

// 1. Check if the key exists before using it
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // ... rest of your code to fetch user data ...
} else {
    // 2. If not logged in, redirect them to login.php
    header("Location: /sp/login");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Use prepared statements for security (prevents SQL Injection)
$stmt = $conn->prepare("SELECT vip_level FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// 3. Check if user_data actually returned a row
if ($user_data) {
    $raw_vip = $user_data['vip_level']; // e.g., "VIP 1"
    $current_vip = (int) filter_var($raw_vip, FILTER_SANITIZE_NUMBER_INT); 
} else {
    $current_vip = 0; // Default if user not found in DB
}

$vip_levels = range(0, 9);
?>

<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP Movie Home UI</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

    <div class="app-container">
        <header class="top-nav">
            
            <img src="./image/logo.png" class="top-logo" alt="Logo">
            <div class="chat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
        </header>

        <div class="banner-container">
            <div class="banner-wrapper" id="bannerSlider">
                <img src="https://spfilm.vip/upload/resource/202505151909406598023989.JPG" alt="Promo 1">
                <img src="https://spfilm.vip/upload/resource/202505151909577755102188.JPG" alt="Promo 2">
                <img src="https://spfilm.vip/upload/resource/202505151910013762523777.JPG" alt="Promo 3">
            </div>
        </div>

        <div class="news-ticker">
            <div class="play-icon-svg">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3l14 9-14 9V3z"></path></svg>
            </div>
            <marquee class="ticker-text" scrollamount="4">အဆင့် - 0 ဝင်ငွေကမ္ဘာသို့ ရောက်ရှိနေပြီဖြစ်သောကြောင့် ယခုပင်ပါဝင်လိုက်ပါ...</marquee>
            <a href="#" class="view-link">ကြည့်ရန်</a>
        </div>

        <div class="menu-grid">
            <div class="menu-item" onclick="location.href='./CompanyProfile'">
                <div class="menu-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                </div>
                <span class="menu-text">ကုမ္ပဏီအကြောင်း</span>
            </div>
            <div class="menu-item">
                <div class="menu-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="2"></rect><path d="M7 2v20M17 2v20M2 12h20"></path></svg>
                </div>
                <span class="menu-text">ဗွီဒီယိုသင်ခန်းစာ</span>
            </div>
            <div class="menu-item">
                <div class="menu-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <span class="menu-text">အွန်လိုင်းဝန်ဆောင်မှု</span>
            </div>
            <div class="menu-item" onclick="location.href='./vip'">
                <div class="menu-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                </div>
                <span class="menu-text">VIP Area</span>
            </div>

            <div class="menu-item" onclick="location.href='./wallet'">
                <div class="menu-icon-box">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
  <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
  <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path>
</svg>    
                </div>
                <span class="menu-text">ငါ့ပိုက်ဆံအိတ်</span>
            </div>
            <div class="menu-item" onclick="location.href='./telegram'">
                <div class="menu-icon-box">
                    <svg width="20px" height="20px" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<circle cx="16" cy="16" r="14" fill="url(#paint0_linear_87_7225)"/>
<path d="M22.9866 10.2088C23.1112 9.40332 22.3454 8.76755 21.6292 9.082L7.36482 15.3448C6.85123 15.5703 6.8888 16.3483 7.42147 16.5179L10.3631 17.4547C10.9246 17.6335 11.5325 17.541 12.0228 17.2023L18.655 12.6203C18.855 12.4821 19.073 12.7665 18.9021 12.9426L14.1281 17.8646C13.665 18.3421 13.7569 19.1512 14.314 19.5005L19.659 22.8523C20.2585 23.2282 21.0297 22.8506 21.1418 22.1261L22.9866 10.2088Z" fill="white"/>
<defs>
<linearGradient id="paint0_linear_87_7225" x1="16" y1="2" x2="16" y2="30" gradientUnits="userSpaceOnUse">
<stop stop-color="#37BBFE"/>
<stop offset="1" stop-color="#007DBB"/>
</linearGradient>
</defs>
</svg>
                </div>
                <span class="menu-text">Telegram Task</span>
            </div>
        </div>

        <div class="section-header">Task Hall</div>
        
       <div class="level-row">
    <?php foreach ($vip_levels as $level): ?>
        <?php 
            // Logic: လက်ရှိ Level က နှိပ်လိုက်တဲ့ Level ထက် ငယ်နေရင် Alert ပြမယ်
            if ($current_vip < $level) {
                $target_url = "javascript:void(0)";
                $onclick = "showVipAlert($level)";
                $btn_class = "btn-outline";
            } else {
                $target_url = "/sp/video?level=" . $level;
                $onclick = "";
                $btn_class = ($current_vip == $level) ? "btn-active" : "btn-outline";
            }
        ?>
        <a href="<?= $target_url ?>" onclick="<?= $onclick ?>" style="text-decoration: none;">
            <div class="level-btn <?= $btn_class ?>">
                VIP <?= $level ?> ▶
            </div>
        </a>
    <?php endforeach; ?>
</div>
        
<div class="movie-grid">

    <div class="card">
        <span class="tag f1">F1</span>
        <img src="https://i.ytimg.com/vi/iirm4u0i53c/hq720.jpg">
        <div class="price">
            <button onclick="play()">▶</button>
            <span>MMK +240.00</span>
        </div>
    </div>

    <div class="card blur">
        <span class="tag f2">F2</span>
        <img src="https://i.ytimg.com/vi/vVAoLgVTm70/hq720.jpg">
        <div class="price">
            <button onclick="play()">▶</button>
            <span>MMK +500.00</span>
        </div>
    </div>

    <div class="card">
        <span class="tag f1">F1</span>
        <img src="https://i.ytimg.com/vi/apysW3Qa8S4/hq720.jpg">
        <div class="price">
            <button onclick="play()">▶</button>
            <span>MMK +240.00</span>
        </div>
    </div>

    <div class="card">
        <span class="tag intern">Intern</span>
        <img src="https://i.ytimg.com/vi/EkmUk0Q89nc/hq720.jpg">
        <div class="price">
            <button onclick="play()">▶</button>
            <span>MMK +500.00</span>
        </div>
    </div>

</div>


        <?php
        include('footer.php');
        ?>
    </div>

    <script>
        // Banner Auto-Slider Script
        const slider = document.getElementById('bannerSlider');
        let currentIdx = 0;
        const totalSlides = 3;

        function slideNext() {
            currentIdx = (currentIdx + 1) % totalSlides;
            slider.style.transform = `translateX(-${(currentIdx * 100) / totalSlides}%)`;
        }

        // ၃ စက္ကန့်လျှင် တစ်ကြိမ် ပုံပြောင်းမည်
        setInterval(slideNext, 3500);

        // Click Effect for Levels (Optional)
        const lvButtons = document.querySelectorAll('.level-btn');
        lvButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                lvButtons.forEach(b => {
                    b.classList.remove('btn-active');
                    b.classList.add('btn-outline');
                });
                btn.classList.add('btn-active');
                btn.classList.remove('btn-outline');
            });
        });
    </script>
    <script>
       document.querySelectorAll('.blur').forEach(card=>{
    card.addEventListener('click',()=>{
        alert('VIP only movie');
    });
});

        </script>
        <script>
function showVipAlert(reqLevel) {
    Swal.fire({
        title: 'VIP ' + reqLevel + ' လိုအပ်ပါသည်',
        text: 'ဤအပိုင်းကိုကြည့်ရှုရန် သင်၏ Level ကို VIP ' + reqLevel + ' သို့ မြှင့်တင်ရန် လိုအပ်ပါသည်။',
        icon: 'lock',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'VIP ဝယ်ယူရန်',
        cancelButtonText: 'ပိတ်မည်'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/sp/vip'; // ဝယ်ယူရန် Page လင့်ခ်
        }
    });
}
</script>
</body>
</html>