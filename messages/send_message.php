<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit();

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);
$file_path = "";

if (!empty($_FILES['file']['name'])) {
    $file_name = time() . "_" . basename($_FILES['file']['name']);
    $target = "messages/" . $file_name;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $file_path = $file_name;
    }
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $file_path);
$stmt->execute();
?>
