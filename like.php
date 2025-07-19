<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo "error";
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Check if user already liked
$sql = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Unlike (delete)
    $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    echo "unliked";
} else {
    // Like (insert)
    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    echo "liked";
}
?>
