<?php
session_start();
header('Content-Type: application/json');
include(__DIR__ . "/../include/config.php");

if(!isset($_SESSION["user_id"])){
    echo json_encode(['status' => 'error', 'message' => 'Login လုပ်ရန် လိုအပ်ပါသည်']);
    exit;
}

$uid = $_SESSION["user_id"];
$amount = $_POST['amount'] ?? 0;
$method = $_POST['method'] ?? '';

if(isset($_FILES['screenshot'])){
    $img_name = $_FILES['screenshot']['name'];
    $tmp_name = $_FILES['screenshot']['tmp_name'];
    
    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    $allowed_ext = ['png', 'jpg', 'jpeg'];

    if(in_array($img_ext, $allowed_ext)){
        // ပုံသိမ်းမည့် Folder နာမည်
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $new_img_name = "REC_" . time() . "_" . $uid . "." . $img_ext;
        
        if(move_uploaded_file($tmp_name, $target_dir . $new_img_name)){
            // Database ထဲ ထည့်ခြင်း (Table Name မှန်အောင် စစ်ပါ)
            $sql = "INSERT INTO recharge_logs (user_id, amount, method, screenshot, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "idss", $uid, $amount, $method, $new_img_name);
            
            if(mysqli_stmt_execute($stmt)){
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ပုံတင်မရဖြစ်နေပါသည်']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ပုံ (JPG, PNG) သာ တင်ပေးပါ']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Screenshot မပါရှိပါ']);
}