<?php
session_start();
// Database connection လမ်းကြောင်း မှန်အောင်ပြင်ပါ
$conn = new mysqli("localhost", "root", "", "movie");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $video_id = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
    $today = date('Y-m-d');

    if ($user_id == 0 || $video_id == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
        exit;
    }

    // ယနေ့အတွက် ဒီဗီဒီယိုကို claim ပြီးပြီလား စစ်ဆေးခြင်း
    $check = $conn->query("SELECT id FROM user_tasks WHERE user_id = $user_id AND video_id = $video_id AND claimed_date = '$today'");

    if ($check->num_rows == 0) {
        // Balance တိုးပေးခြင်း
        $update_balance = $conn->query("UPDATE users SET balance = balance + $amount WHERE id = $user_id");
        // Task မှတ်တမ်းသွင်းခြင်း
        $insert_task = $conn->query("INSERT INTO user_tasks (user_id, video_id, claimed_date, amount) VALUES ($user_id, $video_id, '$today', $amount)");

        if ($update_balance && $insert_task) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database Update Failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ယနေ့အတွက် ဤဗီဒီယိုကြည့်ပြီးပါပြီ']);
    }
}