<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['followed_id'])) {
    header("Location: index.php");
    exit();
}

$follower_id = $_SESSION['user_id'];
$followed_id = intval($_POST['followed_id']);

if ($follower_id == $followed_id) {
    exit("You cannot follow yourself.");
}

// Check if already following
$check = $conn->prepare("SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?");
$check->bind_param("ii", $follower_id, $followed_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Unfollow
    $conn->query("DELETE FROM followers WHERE follower_id = $follower_id AND followed_id = $followed_id");
} else {
    // Follow
$conn->query("INSERT INTO followers (follower_id, followed_id) VALUES ($follower_id, $followed_id)");

// Add notification
$msg = $_SESSION['username'] . " followed you!";
$notif = $conn->prepare("INSERT INTO notifications (user_id, sender_id, message) VALUES (?, ?, ?)");
$notif->bind_param("iis", $followed_id, $follower_id, $msg);
$notif->execute();
}

header("Location: view_profile.php?user_id=$followed_id");
exit();
?>
