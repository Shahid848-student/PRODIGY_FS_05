<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$results = [];

if (isset($_GET['query'])) {
    $query = "%" . $_GET['query'] . "%";
    $stmt = $conn->prepare("SELECT id, username, profile_pic FROM users WHERE username LIKE ? AND id != ?");
    $stmt->bind_param("si", $query, $user_id);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
</head>
<a href="home.php" class="btn btn-outline-dark mb-3"><i class="fa fa-arrow-left"></i> Back</a>
<body class="bg-light">
<div class="container mt-4">
    <a href="home.php" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <form method="get" class="mb-4">
        <input type="text" name="query" class="form-control" placeholder="🔍 Search by username..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
    </form>

    <?php if ($results && $results->num_rows > 0): ?>
        <?php while ($row = $results->fetch_assoc()): ?>
            <div class="card mb-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="uploads/<?php echo $row['profile_pic'] ?: 'default.png'; ?>" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                        <h5 class="mb-0"><?php echo htmlspecialchars($row['username']); ?></h5>
                    </div>
                    <a href="view_profile.php?user_id=<?php echo $row['id']; ?>" class="btn btn-outline-primary">
                        <i class="fa fa-user"></i> View
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php elseif (isset($_GET['query'])): ?>
        <div class="alert alert-warning">No users found.</div>
    <?php endif; ?>
</div>
</body>
</html>
