<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT n.*, u.username AS sender_name 
                        FROM notifications n 
                        JOIN users u ON n.sender_id = u.id 
                        WHERE n.user_id = ? 
                        ORDER BY n.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();

// Mark all as read
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="home.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>
    <h4>🔔 Your Notifications</h4>

    <?php if ($notifications->num_rows > 0): ?>
        <?php while ($row = $notifications->fetch_assoc()): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <strong><?php echo htmlspecialchars($row['sender_name']); ?></strong>
                    <span><?php echo htmlspecialchars($row['message']); ?></span>
                    <small class="text-muted float-end"><?php echo $row['created_at']; ?></small>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No notifications yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
