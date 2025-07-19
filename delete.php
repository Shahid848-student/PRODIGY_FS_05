<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    header("Location: home.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Get image filename
$stmt = $conn->prepare("SELECT image FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$stmt->bind_result($image);
$stmt->fetch();
$stmt->close();

// Delete image file
if ($image) {
    $filePath = "uploads/" . $image;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Delete post (comments and likes will auto-delete now)
$delete = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$delete->bind_param("ii", $post_id, $user_id);
$delete->execute();

header("Location: home.php");
exit();
?>
