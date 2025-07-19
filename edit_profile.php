<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = trim($_POST['bio']);

    // Handle profile picture
    if (!empty($_FILES['profile_pic']['name'])) {
        $img_name = time() . "_" . basename($_FILES['profile_pic']['name']);
        $target = "uploads/" . $img_name;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            $conn->query("UPDATE users SET profile_pic='$img_name' WHERE id=$user_id");
        }
    }

    $stmt = $conn->prepare("UPDATE users SET bio=? WHERE id=?");
    $stmt->bind_param("si", $bio, $user_id);
    $stmt->execute();

    $message = "Profile updated!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="profile.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <div class="card">
        <div class="card-body">
            <h3>Edit Profile</h3>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_pic" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Bio</label>
                    <textarea name="bio" rows="4" class="form-control" placeholder="Write something..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
