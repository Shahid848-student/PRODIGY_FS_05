<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) exit();

$me = $_SESSION['user_id'];
$them = intval($_GET['user_id']);

$stmt = $conn->prepare("SELECT * FROM messages 
                        WHERE (sender_id = ? AND receiver_id = ?) 
                           OR (sender_id = ? AND receiver_id = ?) 
                        ORDER BY sent_at ASC");
$stmt->bind_param("iiii", $me, $them, $them, $me);
$stmt->execute();
$messages = $stmt->get_result();

while ($msg = $messages->fetch_assoc()):
    $class = $msg['sender_id'] == $me ? "me" : "you";
    echo "<div class='chat-message $class'>";
    if ($msg['message']) echo nl2br(htmlspecialchars($msg['message'])) . "<br>";
    if ($msg['file_path']) {
        $ext = pathinfo($msg['file_path'], PATHINFO_EXTENSION);
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            echo "<img src='messages/{$msg['file_path']}' class='chat-img'>";
        } else {
            echo "<a href='messages/{$msg['file_path']}' target='_blank'>Download File</a>";
        }
    }
    echo "<br><small>{$msg['sent_at']}</small></div>";
endwhile;
?>
