<?php
session_start();
header('Content-Type: application/json');
include(__DIR__."/../include/config.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login ဝင်ရန် လိုအပ်သည်']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $task_id = intval($_POST['task_id']);
    $amount = floatval($_POST['amount']);

    // ၁. အရင်လုပ်ပြီးသားလား တစ်ခါထပ်စစ် (Security)
    $stmt = $conn->prepare("SELECT id FROM task_history WHERE user_id = ? AND task_id = ?");
    $stmt->bind_param("ii", $user_id, $task_id);
    $stmt->execute();
    $check_res = $stmt->get_result();

    if ($check_res->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'ဒီ task ကို လုပ်ပြီးသားဖြစ်သည်']);
        exit;
    }

    // Database Transaction စတင်ခြင်း (အမှားမခံအောင်)
    $conn->begin_transaction();

    try {
        // ၂. User balance ကို update လုပ်မယ် (သင့် Table ထဲက column နာမည် balance ဖြစ်ရမယ်)
        $update_sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $up_stmt = $conn->prepare($update_sql);
        $up_stmt->bind_param("di", $amount, $user_id);
        $up_stmt->execute();

        // ၃. Task History ထဲ ထည့်မယ်
        $history_sql = "INSERT INTO task_history (user_id, task_id) VALUES (?, ?)";
        $his_stmt = $conn->prepare($history_sql);
        $his_stmt->bind_param("ii", $user_id, $task_id);
        $his_stmt->execute();

        $conn->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Database Error ဖြစ်သွားသည်']);
    }
}
?>