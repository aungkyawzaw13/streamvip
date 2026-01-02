<?php
session_start();

$conn = mysqli_connect("localhost","root","","movie");
if(!$conn){
    die("DB Error");
}
mysqli_set_charset($conn,"utf8mb4");

if($_SERVER["REQUEST_METHOD"] === "POST"){
    header("Content-Type: application/json; charset=utf-8");

    $phone = trim($_POST["phone"] ?? "");
    $pass  = $_POST["password"] ?? "";

    if($phone=="" || $pass==""){
        echo json_encode([
            "status"=>"error",
            "msg"=>"အချက်အလက်မပြည့်စုံပါ"
        ]);
        exit;
    }

    // 🔐 phone sanitize (DB format = 9884388070)
    $phone = preg_replace('/\D/','',$phone);

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id,password FROM users WHERE phone=? LIMIT 1"
    );

    if(!$stmt){
        echo json_encode([
            "status"=>"error",
            "msg"=>"Server error"
        ]);
        exit;
    }

    mysqli_stmt_bind_param($stmt,"s",$phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($result)){
        if(password_verify($pass, $row["password"])){
            session_regenerate_id(true);
            $_SESSION["user_id"] = $row["id"];

            echo json_encode(["status"=>"success"]);
        }else{
            echo json_encode([
                "status"=>"error",
                "msg"=>"Password မမှန်ပါ"
            ]);
        }
    }else{
        echo json_encode([
            "status"=>"error",
            "msg"=>"Account မရှိပါ"
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP Film Login</title>
    <link rel="stylesheet" href="./css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>

    <div class="movie-grid">
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/gDVgC9jd917NdAcqBdRRDUYi4Tq.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/c0bkO416OU7YGdOFktk45H8REgL.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/c15BtJxCXMrISLVmysdsnZUPQft.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/oJ7g2CifqpStmoYQyaLQgEU32qO.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/udAxQEORq2I5wxI97N2TEqdhzBE.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/cb5NyNrqiCNNoDkA8FfxHAtypdG.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/smxA8yvZ0LzDPer9BIRd4pyOpx1.jpg')"></div>
        <div class="movie-poster" style="background-image: url('https://media.themoviedb.org/t/p/w220_and_h330_face/iB64vpL3dIObOtMZgX3RqdVdQDc.jpg')"></div>
    </div>
    <div class="overlay-dark"></div>

    <div class="login-container">
        <div class="logo">
            <img src="./image/logo.png" alt="Logo">
        </div>

        <div class="input-group">
            <div class="dropdown-trigger" id="open-sheet">
                <span id="selected-code">+95</span>
                <svg width="10" height="10" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>
            </div>
            <input type="number" id="phone" placeholder="ကျေးဇူးပြုပြီးသင့်ဖုန်းနံပါတ်ကိုရိုက်ထည့်ပါ">
        </div>

        <div class="input-group">
            <span class="icon-svg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </span>
            <input type="password" id="password" placeholder="Login password ထည့်ပါ">
        </div>

        <button class="btn-login" onclick="login()">ယခု Log In ဝင်ပါ</button>
        <button class="btn-register" onclick="location.href='./register'">ယခု မှတ်ပုံတင်ပါ</button>
    </div>

    <div class="sheet-overlay" id="overlay"></div>
    <div class="bottom-sheet" id="sheet">
        <div class="sheet-item" data-code="+95">🇲🇲 Myanmar (+95)</div>
        <div class="sheet-item" data-code="+91">🇮🇳 India (+91)</div>
        <div class="sheet-item" data-code="+66">🇹🇭 Thailand (+66)</div>
        <div class="sheet-close" id="close-sheet">Cancel</div>
    </div>

    <script>
        const openBtn = document.getElementById('open-sheet');
        const closeBtn = document.getElementById('close-sheet');
        const overlay = document.getElementById('overlay');
        const sheet = document.getElementById('sheet');
        const selectedCode = document.getElementById('selected-code');
        const items = document.querySelectorAll('.sheet-item');

        // ပွင့်လာစေရန်
        openBtn.addEventListener('click', () => {
            overlay.style.display = 'block';
            setTimeout(() => sheet.classList.add('active'), 10);
        });

        // ပြန်ပိတ်ရန်
        const closeSheet = () => {
            sheet.classList.remove('active');
            setTimeout(() => overlay.style.display = 'none', 300);
        };

        overlay.addEventListener('click', closeSheet);
        closeBtn.addEventListener('click', closeSheet);

        // ကုဒ်ရွေးချယ်ခြင်း
        items.forEach(item => {
            item.addEventListener('click', function() {
                selectedCode.innerText = this.getAttribute('data-code');
                closeSheet();
            });
        });
    </script>

<script>
document.addEventListener("DOMContentLoaded", ()=>{

    const openBtn = document.getElementById('open-sheet');
    const closeBtn = document.getElementById('close-sheet');
    const overlay = document.getElementById('overlay');
    const sheet = document.getElementById('sheet');
    const selectedCode = document.getElementById('selected-code');
    const items = document.querySelectorAll('.sheet-item');

    openBtn.onclick = ()=>{
        overlay.style.display='block';
        setTimeout(()=>sheet.classList.add('active'),10);
    };

    const closeSheet = ()=>{
        sheet.classList.remove('active');
        setTimeout(()=>overlay.style.display='none',300);
    };

    overlay.onclick = closeSheet;
    closeBtn.onclick = closeSheet;

    items.forEach(item=>{
        item.onclick = ()=>{
            selectedCode.innerText = item.dataset.code;
            closeSheet();
        };
    });

    window.login = function(){
        const phone = document.getElementById("phone").value.trim();
        const pass  = document.getElementById("password").value;

        if(!phone || !pass){
            Swal.fire("Error","အချက်အလက်မပြည့်စုံပါ","warning");
            return;
        }

        Swal.fire({
            title:"Checking...",
            allowOutsideClick:false,
            didOpen:()=>Swal.showLoading()
        });

        fetch("login.php",{
            method:"POST",
            headers:{ "Content-Type":"application/x-www-form-urlencoded" },
            body:new URLSearchParams({
                phone:phone,
                password:pass
            })
        })
        .then(res=>res.json())
        .then(data=>{
            Swal.close();
            if(data.status==="success"){
                Swal.fire({
                    icon:"success",
                    title:"Login အောင်မြင်ပါသည် 🎉",
                    timer:1200,
                    showConfirmButton:false
                }).then(()=>location.href="./profile");
            }else{
                Swal.fire("Error",data.msg,"error");
            }
        });
    };

});
</script>


</body>
</html>