<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caption = trim($_POST["caption"]);
    $imageName = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Rename file to avoid conflicts
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetPath = "uploads/" . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            // Insert post into DB
            $stmt = $conn->prepare("INSERT INTO posts (user_id, image, caption) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $imageName, $caption);
            $stmt->execute();
            header("Location: home.php");
            exit();
        } else {
            $error = "❌ Image upload failed. Please try again.";
        }
    } else {
        $error = "⚠️ No image selected or an upload error occurred.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="home.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <h3>📤 Upload a Post</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Choose an image</label>
            <input type="file" name="image" class="form-control" required accept="image/*">
        </div>
        <div class="mb-3">
            <label class="form-label">Write a caption</label>
            <textarea name="caption" class="form-control" rows="2" placeholder="Say something..."></textarea>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
    </form>
</div>

</body>
</html>
