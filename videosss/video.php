<?php
session_start();
// Database ချိတ်ဆက်မှု - လမ်းကြောင်းမှန်အောင် စစ်ဆေးပါ
$conn = new mysqli("localhost", "root", "", "movie");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. User & VIP Data Fetching
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) { header("Location: login.php"); exit(); }

$user_query = "SELECT username, vip_level, balance FROM users WHERE id = '$user_id'";
$u_result = $conn->query($user_query);
$u_data = $u_result->fetch_assoc();

$current_vip_raw = $u_data['vip_level'] ?? 'VIP 0';
// "VIP 1" ထဲက နံပါတ် "1" ကိုပဲ ထုတ်ယူခြင်း
$current_vip_num = (int) filter_var($current_vip_raw, FILTER_SANITIZE_NUMBER_INT);

// URL မှ Level နှင့် Video ID ကိုယူခြင်း
$requested_level = isset($_GET['level']) ? (int)$_GET['level'] : $current_vip_num;
$vid = isset($_GET['vid']) ? (int)$_GET['vid'] : 0;

// 2. Security Check (ကိုယ့် Level ထက်မြင့်တာ ကြည့်ခွင့်မပေးရန်)
if ($requested_level > $current_vip_num) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
        body { background-color: #000 !important; } /* Alert ပေါ်နေတုန်း နောက်ခံမမည်းသွားအောင် */
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Access Denied!',
                text: 'ဤဗီဒီယိုကိုကြည့်ရန် VIP Level $requested_level အဆင့်ရှိရန် လိုအပ်ပါသည်။',
                icon: 'error',
                background: '#141414',
                color: '#fff',
                confirmButtonColor: '#e50914',
                confirmButtonText: 'Upgrade လုပ်ရန်သွားမည်',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/vip';
                }
            });
        });
    </script>";
    exit();
}

// 3. Task Limit Logic (VIP အလိုက် တစ်နေ့တာ ကြည့်နိုင်သည့် အရေအတွက်)
$task_limits = [0 => 2, 1 => 5, 2 => 10, 3 => 15]; // VIP 0 = 2 ခု
$limit = $task_limits[$current_vip_num] ?? 0;
$today = date('Y-m-d');

// ယနေ့ Claim ပြီးသမျှ အရေအတွက် စစ်ဆေးခြင်း
$done_res = $conn->query("SELECT COUNT(*) as total FROM user_tasks WHERE user_id = $user_id AND claimed_date = '$today'");
$done_count = $done_res->fetch_assoc()['total'];

// 4. Fetch All Videos for the Sidebar
// သင် Admin မှာ vip_name (ဥပမာ VIP 0) လို့ သိမ်းထားရင် ၎င်းအတိုင်း ရှာဖွေပါ
$v_list_sql = "SELECT * FROM vip_videos WHERE vip_name = 'VIP $requested_level' ORDER BY video_id DESC";
$v_list_result = $conn->query($v_list_sql);
$videos = $v_list_result->fetch_all(MYSQLI_ASSOC);

// 5. Active Video ကို သတ်မှတ်ခြင်း
$active_video = null;
if ($vid > 0) {
    foreach($videos as $v) {
        if($v['video_id'] == $vid) { $active_video = $v; break; }
    }
}
// vid မပါလျှင် ပထမဆုံး ဗီဒီယိုကို Active လုပ်မည်
if (!$active_video && count($videos) > 0) {
    $active_video = $videos[0];
}

// 6. လက်ရှိဗီဒီယိုကို Claim ပြီးသားလား စစ်ဆေးခြင်း
$is_claimed = false;
if ($active_video) {
    $active_id = $active_video['video_id'];
    $check_claim = $conn->query("SELECT id FROM user_tasks WHERE user_id = $user_id AND video_id = $active_id AND claimed_date = '$today'");
    $is_claimed = ($check_claim->num_rows > 0);
}

// Duration ကို စက္ကန့်ပြောင်းခြင်း
$duration_sec = 0;
if ($active_video) {
    $parts = explode(':', $active_video['duration']);
    $duration_sec = (isset($parts[0]) ? (int)$parts[0] * 60 : 0) + (isset($parts[1]) ? (int)$parts[1] : 0);
}

// YouTube ID ထုတ်ယူခြင်း
$yt_id = "";
if ($active_video) {
    parse_str(parse_url($active_video['video_url'], PHP_URL_QUERY), $yt_params);
    $yt_id = $yt_params['v'] ?? "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stream VIP - <?= htmlspecialchars($active_video['video_title'] ?? 'Player') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary-color: #e50914; --bg-color: #000000; --card-bg: #141414; }
        body { background-color: var(--bg-color); color: #fff; font-family: 'Inter', sans-serif; }
     


        .video-stage { background: #000; border-radius: 12px; overflow: hidden; position: relative; }
        .video-wrapper { position: relative; padding-top: 56.25%; }
        #player { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .video-card { background: var(--card-bg); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .list-item { display: flex; gap: 12px; padding: 10px; border-radius: 8px; text-decoration: none; color: #ccc; transition: 0.2s; border-bottom: 1px solid #222; }
        .list-item:hover { background: #222; color: #fff; }
        .list-item.active { background: #2f2f2f; border-left: 4px solid var(--primary-color); }
        .thumb-box { width: 100px; height: 60px; background: #333; border-radius: 5px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .claim-btn { background: #28a745; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; width: 100%; display: none; }
        .timer-box { background: rgba(229, 9, 20, 0.1); border: 1px solid var(--primary-color); color: var(--primary-color); padding: 10px; border-radius: 8px; text-align: center; }
    </style>
</head>
<body>
<div class="app-container">
<div class="container mt-4">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="h3 text-danger fw-bold text-decoration-none">STREAM VIP</a>
        <div class="text-end">
            <span class="badge bg-danger mb-1">VIP <?= $current_vip_num ?></span>
            <div class="small">Daily Task: <?= $done_count ?> / <?= $limit ?></div>
        </div>
    </header>

    <div class="row">
        <div class="col-lg-8">
            <div class="video-stage shadow-lg">
                <div class="video-wrapper">
                    <div id="player"></div>
                </div>
            </div>

            <div class="video-card mt-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4><?= htmlspecialchars($active_video['video_title'] ?? 'Video Not Found') ?></h4>
                        <p class="text-secondary"><?= nl2br(htmlspecialchars($active_video['description'] ?? '')) ?></p>
                    </div>
                    <div style="min-width: 180px;">
                        <?php if ($is_claimed): ?>
                            <div class="alert alert-secondary py-2 text-center">ကြည့်ပြီးသားပါ</div>
                        <?php elseif ($done_count >= $limit): ?>
                            <div class="alert alert-warning py-2 text-center small">Daily Limit ပြည့်ပါပြီ</div>
                        <?php else: ?>
                            <div id="status-area">
                                <div class="timer-box mb-2" id="timer-ui">
                                    စောင့်ရန်: <span id="secs"><?= $duration_sec ?></span>s
                                </div>
                                <button id="claimBtn" class="claim-btn" onclick="claimReward()">Claim MMK <?= number_format($active_video['price']) ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="video-card">
                <h6 class="mb-3 text-warning"><i class="fas fa-list me-2"></i> VIP Videos List</h6>
                <div style="max-height: 600px; overflow-y: auto;">
                    <?php if (count($videos) > 0): ?>
                        <?php foreach ($videos as $v): ?>
                            <?php 
                                $v_id = $v['video_id'];
                                $is_v_done = ($conn->query("SELECT id FROM user_tasks WHERE user_id = $user_id AND video_id = $v_id AND claimed_date = '$today'")->num_rows > 0);
                                $is_active = ($active_video['video_id'] == $v_id) ? 'active' : '';
                            ?>
                            <a href="?level=<?= $requested_level ?>&vid=<?= $v_id ?>" class="list-item <?= $is_active ?>">
                                <div class="thumb-box">
                                    <?php if ($is_v_done): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-play text-secondary"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="info">
                                    <div class="small fw-bold <?= $is_active ? 'text-danger' : 'text-white' ?>"><?= htmlspecialchars($v['video_title']) ?></div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        <?= $v['duration'] ?> | <span class="text-success">+<?= $v['price'] ?> MMK</span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">ဗီဒီယိုမရှိသေးပါ။</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    let player;
    let timeLeft = <?= $duration_sec ?>;
    let timerInterval;
    let isClaimed = <?= $is_claimed ? 'true' : 'false' ?>;

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            videoId: '<?= $yt_id ?>',
            playerVars: { 'autoplay': 0, 'rel': 0, 'showinfo': 0 },
            events: { 'onStateChange': onPlayerStateChange }
        });
    }

    function onPlayerStateChange(event) {
        // Video Play ဖြစ်မှ Timer စမောင်းမည်။ Pause ဖြစ်လျှင် ရပ်မည်။
        if (event.data == YT.PlayerState.PLAYING && !isClaimed) {
            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer-ui').style.display = 'none';
                    let btn = document.getElementById('claimBtn');
                    if(btn) btn.style.display = 'block';
                } else {
                    timeLeft--;
                    document.getElementById('secs').innerText = timeLeft;
                }
            }, 1000);
        } else {
            clearInterval(timerInterval);
        }
    }

    function claimReward() {
    fetch('/sp/videosss/claim_process.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `video_id=<?= $active_video['video_id'] ?>&amount=<?= $active_video['price'] ?>`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                title: 'အောင်မြင်ပါသည်!',
                text: 'Reward MMK <?= number_format($active_video['price']) ?> ကို လက်ခံရရှိပြီးပါပြီ။',
                icon: 'success',
                confirmButtonColor: '#e50914',
                confirmButtonText: 'အိုကေ'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // အိုကေနှိပ်မှ page ကို reload လုပ်မယ်
                }
            });
        } else {
            Swal.fire({
                title: 'မှားယွင်းနေပါသည်!',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#e50914'
            });
        }
    })
    .catch(err => {
        Swal.fire('Error!', 'ချိတ်ဆက်မှု မအောင်မြင်ပါ', 'error');
    });
}


</script>
</body>
</html>