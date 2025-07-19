<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['post_id'])) {
    header("Location: home.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_GET['post_id']);

// Check if post exists
$stmt = $conn->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>❌ Post not found or deleted.</div>";
    exit();
}

$post = $result->fetch_assoc();

// Handle new comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
    header("Location: comment.php?post_id=$post_id");
    exit();
}

// Fetch comments
$comments = $conn->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC");
$comments->bind_param("i", $post_id);
$comments->execute();
$commentResults = $comments->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="home.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <div class="card mb-4">
        <div class="card-header">
            <strong><?php echo htmlspecialchars($post['username']); ?></strong>
        </div>
        <div class="card-body">
            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid mb-2" style="max-height: 400px;">
            <p><?php echo htmlspecialchars($post['caption']); ?></p>
        </div>
    </div>

    <h5>💬 Comments</h5>

    <?php while ($row = $commentResults->fetch_assoc()): ?>
        <div class="card mb-2">
            <div class="card-body">
                <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
                <?php echo nl2br(htmlspecialchars($row['comment'])); ?>
                <div class="text-muted small mt-1"><?php echo $row['created_at']; ?></div>
            </div>
        </div>
    <?php endwhile; ?>

    <form method="post" class="mt-3">
        <div class="mb-3">
            <textarea name="comment" class="form-control" rows="2" placeholder="Write a comment..." required></textarea>
        </div>
        <button class="btn btn-primary"><i class="fa fa-comment"></i> Post Comment</button>
    </form>
</div>

</body>
</html>
