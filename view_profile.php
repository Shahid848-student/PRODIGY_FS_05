<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    header("Location: index.php");
    exit();
}

$current_user = $_SESSION['user_id'];
$viewed_id = intval($_GET['user_id']);

$stmt = $conn->prepare("SELECT username, profile_pic, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $viewed_id);
$stmt->execute();
$stmt->bind_result($username, $profile_pic, $bio);
$stmt->fetch();
$stmt->close();

// Check if current user already follows
$check = $conn->prepare("SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?");
$check->bind_param("ii", $current_user, $viewed_id);
$check->execute();
$is_following = $check->get_result()->num_rows > 0;
$check->close();

// Follower/Following counts
$follower_count = $conn->query("SELECT COUNT(*) FROM followers WHERE followed_id = $viewed_id")->fetch_row()[0];
$following_count = $conn->query("SELECT COUNT(*) FROM followers WHERE follower_id = $viewed_id")->fetch_row()[0];

// Fetch posts by that user
$posts = $conn->query("SELECT * FROM posts WHERE user_id = $viewed_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($username); ?>'s Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
</head>
<a href="home.php" class="btn btn-outline-dark mb-3"><i class="fa fa-arrow-left"></i> Back</a>
<body class="bg-light">

<div class="container mt-4">
    <a href="search.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <div class="card mb-3 text-center">
        <div class="card-body">
            <img src="uploads/<?php echo $profile_pic ?: 'default.png'; ?>" class="rounded-circle mb-2" width="100" height="100" style="object-fit: cover;">
            <h4><?php echo htmlspecialchars($username); ?></h4>
            <p class="text-muted"><?php echo nl2br(htmlspecialchars($bio)); ?></p>

            <div class="d-flex justify-content-center gap-5 my-2">
                <div><strong><?php echo $follower_count; ?></strong><br>Followers</div>
                <div><strong><?php echo $following_count; ?></strong><br>Following</div>
            </div>

            <?php if ($current_user != $viewed_id): ?>
                <form action="follow.php" method="post">
                    <input type="hidden" name="followed_id" value="<?php echo $viewed_id; ?>">
                    <button type="submit" class="btn btn-<?php echo $is_following ? 'danger' : 'primary'; ?>">
                        <i class="fa <?php echo $is_following ? 'fa-user-minus' : 'fa-user-plus'; ?>"></i>
                        <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <h5><?php echo htmlspecialchars($username); ?>'s Posts</h5>
    <?php if ($posts->num_rows > 0): ?>
        <?php while ($row = $posts->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <img src="uploads/<?php echo $row['image']; ?>" class="img-fluid mb-2" style="max-height:300px;">
                    <p><?php echo htmlspecialchars($row['caption']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No posts uploaded.</div>
    <?php endif; ?>
</div>

</body>
</html>
