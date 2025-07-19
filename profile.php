<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT profile_pic, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic, $bio);
$stmt->fetch();
$stmt->close();

// Fetch follower and following count
$follower_count = $conn->query("SELECT COUNT(*) FROM followers WHERE followed_id = $user_id")->fetch_row()[0];
$following_count = $conn->query("SELECT COUNT(*) FROM followers WHERE follower_id = $user_id")->fetch_row()[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<a href="home.php" class="btn btn-outline-dark mb-3"><i class="fa fa-arrow-left"></i> Back</a>
<body class="bg-light">

<div class="container mt-4">
    <a href="home.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <div class="card">
        <div class="card-body text-center">
            <img src="uploads/<?php echo $profile_pic ? $profile_pic : 'default.png'; ?>" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
            <h3><?php echo htmlspecialchars($username); ?></h3>
            <p class="text-muted"><?php echo nl2br(htmlspecialchars($bio)); ?></p>

            <div class="d-flex justify-content-center gap-5 my-3">
                <div><strong><?php echo $follower_count; ?></strong><br>Followers</div>
                <div><strong><?php echo $following_count; ?></strong><br>Following</div>
            </div>

            <a href="edit_profile.php" class="btn btn-outline-primary"><i class="fa fa-user-edit"></i> Edit Profile</a>
        </div>
    </div>
</div>

</body>
</html>
