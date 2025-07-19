<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$posts = $conn->query("SELECT p.*, u.username, u.profile_pic FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");

function likedByUser($conn, $post_id, $user_id) {
    $check = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $check->bind_param("ii", $post_id, $user_id);
    $check->execute();
    return $check->get_result()->num_rows > 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Social App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        body {
            background: url('uploads/bg_doodles.png') repeat;
            background-size: 400px;
            font-family: 'Segoe UI', sans-serif;
            scroll-behavior: smooth;
        }

        .welcome {
            font-family: 'Brush Script MT', cursive;
            font-size: 2rem;
            color: #673ab7;
            text-shadow: 1px 1px #ddd;
        }

        .post-img {
            max-height: 400px;
            object-fit: cover;
            width: 100%;
        }

        .post-card:hover {
            transform: scale(1.01);
            transition: 0.3s;
            box-shadow: 0px 0px 10px #ccc;
        }

        .like-btn, .comment-btn, .delete-btn {
            cursor: pointer;
            margin-right: 10px;
            transition: transform 0.2s;
        }

        .like-btn:hover, .comment-btn:hover {
            transform: scale(1.2);
        }

        .navbar-btn {
            margin-right: 10px;
        }

        .liked {
            color: red;
        }
    </style>

    <script>
        function like(postId) {
            fetch('like.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'post_id=' + postId
            }).then(() => location.reload());
        }

        function doubleTapLike(e, postId) {
            let now = new Date().getTime();
            if (e.target.dataset.lastTap && now - e.target.dataset.lastTap < 300) {
                like(postId);
            }
            e.target.dataset.lastTap = now;
        }
    </script>
</head>
<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="welcome">👋 Welcome, <?php echo htmlspecialchars($username); ?></div>
        <div>
            <a href="upload.php" class="btn btn-success navbar-btn"><i class="fa fa-plus"></i> Upload</a>
            <a href="profile.php" class="btn btn-primary navbar-btn"><i class="fa fa-user"></i></a>
            <a href="search.php" class="btn btn-warning navbar-btn"><i class="fa fa-search"></i></a>
            <a href="notifications.php" class="btn btn-secondary navbar-btn"><i class="fa fa-bell"></i></a>
            <a href="logout.php" class="btn btn-danger navbar-btn"><i class="fa fa-sign-out-alt"></i></a>
        </div>
    </div>

    <?php while ($row = $posts->fetch_assoc()): ?>
        <div class="card post-card mb-4">
            <div class="card-header d-flex align-items-center">
                <img src="uploads/<?php echo $row['profile_pic'] ?: 'default.png'; ?>" width="40" height="40" class="rounded-circle me-2" style="object-fit:cover;">
                <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                <?php if ($row['user_id'] == $user_id): ?>
                    <form action="delete.php" method="post" class="ms-auto">
                        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body" onclick="doubleTapLike(event, <?php echo $row['id']; ?>)">
                <img src="uploads/<?php echo $row['image']; ?>" class="post-img mb-2">
                <p><?php echo htmlspecialchars($row['caption']); ?></p>
                <div>
                    <span class="like-btn <?php echo likedByUser($conn, $row['id'], $user_id) ? 'liked' : ''; ?>" onclick="like(<?php echo $row['id']; ?>)">
                        <i class="fa fa-heart"></i> 
                        <?php
                            $count = $conn->query("SELECT COUNT(*) FROM likes WHERE post_id = {$row['id']}")->fetch_row()[0];
                            echo $count;
                        ?>
                    </span>
                    <a href="comment.php?post_id=<?php echo $row['id']; ?>" class="comment-btn text-decoration-none text-dark">
                        <i class="fa fa-comment"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

</div>
</body>
</html>
