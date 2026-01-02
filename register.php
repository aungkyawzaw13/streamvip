<?php
ob_start();

/* ===============================
   DATABASE CONFIG
================================ */
$conn = mysqli_connect("localhost", "root", "", "movie");

/* ===============================
   REGISTER API
================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    ob_clean();
    header("Content-Type: application/json");

    if (!$conn) {
        echo json_encode(["status"=>"error","msg"=>"Database connection failed"]);
        exit;
    }

    $phone_code = trim($_POST["phone_code"] ?? "");
    $phone      = trim($_POST["phone"] ?? "");
    $pass1      = $_POST["password"] ?? "";
    $pass2      = $_POST["confirm_password"] ?? "";
    $invite     = trim($_POST["invite_code"] ?? "");

    if ($phone=="" || $phone_code=="" || $pass1=="" || $pass2=="") {
        echo json_encode(["status"=>"error","msg"=>"အချက်အလက်အားလုံး ဖြည့်ပါ"]);
        exit;
    }

    if ($pass1 !== $pass2) {
        echo json_encode(["status"=>"error","msg"=>"စကားဝှက် မကိုက်ညီပါ"]);
        exit;
    }

    if (strlen($pass1) < 6) {
        echo json_encode(["status"=>"error","msg"=>"စကားဝှက် အနည်းဆုံး 6 လုံးလိုအပ်ပါသည်"]);
        exit;
    }

    $phone = ltrim($phone, "0");

    $chk = mysqli_prepare($conn,
        "SELECT id FROM users WHERE phone=? AND phone_code=? LIMIT 1"
    );
    mysqli_stmt_bind_param($chk, "ss", $phone, $phone_code);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);

    if (mysqli_stmt_num_rows($chk) > 0) {
        echo json_encode(["status"=>"error","msg"=>"ဒီဖုန်းနံပါတ်ရှိပြီးသားပါ"]);
        exit;
    }
    mysqli_stmt_close($chk);

    $hash = password_hash($pass1, PASSWORD_DEFAULT);
    $invite = $invite !== "" ? $invite : null;
    $balance = 0;

    $stmt = mysqli_prepare($conn,
        "INSERT INTO users
        (phone, phone_code, password, balance, invite_code, creat_at)
        VALUES (?, ?, ?, ?, ?, NOW())"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssds",
        $phone,
        $phone_code,
        $hash,
        $balance,
        $invite
    );

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status"=>"success"]);
    } else {
        echo json_encode(["status"=>"error","msg"=>"Register failed"]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="my">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SP Film Register</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="./css/login.css">
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
      <span id="selected-code">+95</span> ▼
    </div>
    <input type="tel" id="phone" placeholder="ဖုန်းနံပါတ်">
  </div>

  <div class="input-group">
    <input type="password" id="password" placeholder="စကားဝှက်">
  </div>

  <div class="input-group">
    <input type="password" id="confirm_password" placeholder="စကားဝှက်အတည်ပြု">
  </div>

  <div class="input-group">
    <input type="text" id="invite_code" placeholder="Invite Code (optional)">
  </div>

  <button class="btn-login" onclick="register()">ယခုမှတ်ပုံတင်ပါ</button>
  <button class="btn-register" onclick="location.href='./login'">လော့ဂ်အင်</button>
</div>

<div class="sheet-overlay" id="overlay"></div>
<div class="bottom-sheet" id="sheet">
  <div class="sheet-item" data-code="+95">🇲🇲 Myanmar (+95)</div>
  <div class="sheet-item" data-code="+66">🇹🇭 Thailand (+66)</div>
  <div class="sheet-item" data-code="+91">🇮🇳 India (+91)</div>
  <div class="sheet-close" id="close-sheet">Cancel</div>
</div>

<script>
const openBtn=document.getElementById("open-sheet");
const overlay=document.getElementById("overlay");
const sheet=document.getElementById("sheet");
const selectedCode=document.getElementById("selected-code");

openBtn.onclick=()=>{overlay.style.display="block";sheet.classList.add("active")}
overlay.onclick=()=>{sheet.classList.remove("active");overlay.style.display="none"}
document.getElementById("close-sheet").onclick=overlay.onclick;

document.querySelectorAll(".sheet-item").forEach(i=>{
  i.onclick=()=>{selectedCode.innerText=i.dataset.code;overlay.onclick()}
});

function register(){
  const phone_code=selectedCode.innerText.trim();
  const phone=document.getElementById("phone").value.trim();
  const p1=document.getElementById("password").value;
  const p2=document.getElementById("confirm_password").value;
  const invite=document.getElementById("invite_code").value.trim();

  if(!phone||!p1||!p2){
    Swal.fire("Error","အချက်အလက်အားလုံး ဖြည့်ပါ","warning");return;
  }

  Swal.fire({title:"Processing...",allowOutsideClick:false,didOpen:()=>Swal.showLoading()});

  const f=new URLSearchParams();
  f.append("phone_code",phone_code);
  f.append("phone",phone);
  f.append("password",p1);
  f.append("confirm_password",p2);
  f.append("invite_code",invite);

  fetch("",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:f})
  .then(r=>r.json())
  .then(d=>{
    Swal.close();
    if(d.status==="success"){
      Swal.fire("Success","Register အောင်မြင်ပါသည် 🎉","success")
      .then(()=>location.href="./login");
    }else{
      Swal.fire("Error",d.msg,"error");
    }
  });
}
</script>

</body>
</html>
