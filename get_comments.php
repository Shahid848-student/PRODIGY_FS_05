<?php
include 'db.php';

if (!isset($_GET['post_id'])) {
    exit("Invalid request");
}

$post_id = intval($_GET['post_id']);

$sql = "SELECT comments.comment, comments.created_at, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ?
        ORDER BY comments.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
    <div class="border-bottom py-1">
        <strong><?php echo htmlspecialchars($row['username']); ?>:</strong>
        <?php echo nl2br(htmlspecialchars($row['comment'])); ?>
        <br><small class="text-muted"><?php echo $row['created_at']; ?></small>
    </div>
<?php endwhile; ?>
