<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}
include(__DIR__."/../include/config.php");

$user_id = $_SESSION["user_id"];

// Task list နဲ့ User လုပ်ပြီးသား ဟုတ်မဟုတ် JOIN စစ်မယ်
$sql = "SELECT t.*, h.id as is_done 
        FROM telegram t 
        LEFT JOIN task_history h ON t.id = h.task_id AND h.user_id = ? 
        ORDER BY t.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Tasks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./telegramtasks/style.css">
    <style>
        /* ခလုတ်တွေ ပိုလှအောင် CSS အနည်းငယ် ထပ်ဖြည့်ထားပါတယ် */
        .done-btn { background: #6c757d !important; cursor: not-allowed; }
        .claim-btn { background: #28a745 !important; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <i class="fas fa-chevron-left" onclick="history.back()"></i>
        <span>Telegram Tasks</span>
    </div>

    <div class="vip-container">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($vip = $result->fetch_assoc()): ?>
                <div class="vip-card <?= ($vip['status'] == 0 || $vip['is_done']) ? 'locked' : '' ?>">
                    <div class="vip-badge">
                        <img src="/sp/image/telegram/tele.png" alt="VIP Icon">
                    </div>
                    <div class="vip-info">
                        <h2 class="vip-title"><?= htmlspecialchars($vip['text']) ?></h2>
                        <p class="vip-perks">MMK <?= htmlspecialchars($vip['pay']) ?>.00</p>
                    </div>

                    <div id="action-area-<?= $vip['id'] ?>">
                        <?php if($vip['is_done']): ?>
                            <button class="buy-btn done-btn" disabled>အောင်မြင်ပြီ</button>
                        <?php else: ?>
                            <button class="buy-btn" id="btn-<?= $vip['id'] ?>" 
                                onclick="startTask(<?= $vip['id'] ?>, '<?= htmlspecialchars($vip['link']) ?>', <?= $vip['pay'] ?>)">
                                Open
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:white;text-align:center;">Tasks မရှိပါ</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function startTask(taskId, link, amount) {
    // ၁. Link ကို Tab အသစ်မှာ ဖွင့်မယ်
    window.open(link, '_blank');

    let actionArea = document.getElementById('action-area-' + taskId);
    let timeLeft = 60; // ၁ မိနစ်

    // ၂. ခလုတ်ကို Timer ပြောင်းမယ်
    actionArea.innerHTML = `<button class="buy-btn" disabled style="background:#f39c12;">စောင့်ရန် (<span id="timer-${taskId}">60</span>s)</button>`;

    let timerInterval = setInterval(() => {
        timeLeft--;
        let timerSpan = document.getElementById('timer-' + taskId);
        if(timerSpan) timerSpan.innerText = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            // ၃. Claim Button ပြောင်းမယ်
            actionArea.innerHTML = `<button class="buy-btn claim-btn" onclick="claimReward(${taskId}, ${amount})">Claim MMK ${amount}</button>`;
        }
    }, 1000);
}

function claimReward(taskId, amount) {
    fetch('/sp/telegramtasks/claim_task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `task_id=${taskId}&amount=${amount}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                title: 'အောင်မြင်ပါသည်!',
                text: 'MMK ' + amount + ' ကို သင့်လက်ကျန်ငွေထဲ ပေါင်းထည့်လိုက်ပါပြီ။',
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('မှားယွင်းမှု', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Server ချိတ်ဆက်မှု မရပါ', 'error');
    });
}
</script>
</body>
</html>