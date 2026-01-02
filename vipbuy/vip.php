<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ./login");
    exit;
}
?>
<?php
include(__DIR__."/../include/config.php");
// VIP list
$sql = "SELECT id, vipphoto, title, price, dailytasks, pay, status FROM vip ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP Card UI</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./vipbuy/style.css">
</head>
<body>

<div class="app-container">
    <div class="header">
        <i class="fas fa-chevron-left" onclick="history.back()"></i>
        <span>VIP အဆင့်များ</span>
    </div>

    <div class="vip-container">

        <?php if($result && $result->num_rows > 0): ?>
            <?php while($vip = $result->fetch_assoc()): ?>

                <div class="vip-card <?= $vip['status'] == 0 ? 'locked' : '' ?>">

                    <div class="vip-badge">
                        <img src="./sp/<?= htmlspecialchars($vip['vipphoto']) ?>" alt="VIP Icon">
                    </div>

                    <div class="vip-info">
                        <h2 class="vip-title"><?= htmlspecialchars($vip['title']) ?></h2>
                        <p class="vip-price">
                            ရင်းနှီးမြှုပ်နှံမှု: MMK <?= number_format($vip['price']) ?>
                        </p>
                        <p class="vip-perks">
                            နေ့စဉ်အလုပ်များ: <?= htmlspecialchars($vip['dailytasks']) ?> ခု
                        </p>
                        <p class="vip-perks">
                            အမိန့်တစ်ခုနှုန်း: MMK<?= htmlspecialchars($vip['pay']) ?> 
                        </p>
                    </div>

                    <div class="vip-action">
                        <?php if($vip['status'] == 1): ?>
                            <button class="buy-btn"
                                onclick="buyVIP(<?= $vip['id'] ?>)">
                                အခုဝယ်မည်
                            </button>
                        <?php else: ?>
                            <button class="buy-btn disabled" disabled>
                                မရရှိနိုင်ပါ
                            </button>
                        <?php endif; ?>
                    </div>

                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:white;text-align:center;">VIP မရှိပါ</p>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function buyVIP(vipId) {
    Swal.fire({
        title: 'VIP ဝယ်မည်လား?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ဝယ်မည်',
        cancelButtonText: 'မဝယ်တော့ပါ'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/sp/buyvip?vip_id=" + vipId;
        }
    });
}
</script>

</body>
</html>
