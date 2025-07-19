<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    header("Location: index.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$friend_id = intval($_GET['user_id']);

// ✅ Check if mutual follow
$follow1 = $conn->query("SELECT * FROM followers WHERE follower_id = $my_id AND followed_id = $friend_id")->num_rows;
$follow2 = $conn->query("SELECT * FROM followers WHERE follower_id = $friend_id AND followed_id = $my_id")->num_rows;

if (!$follow1 || !$follow2) {
    die("Chat only available after mutual follow.");
}

// Get friend's name
$friend = $conn->query("SELECT username FROM users WHERE id = $friend_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with <?php echo htmlspecialchars($friend['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        .chat-box {
            height: 400px;
            overflow-y: scroll;
            border: 1px solid #ccc;
            padding: 10px;
            background: #fdfdfd;
            border-radius: 10px;
        }

        .chat-message {
            padding: 5px 10px;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .me {
            background: #d1e7dd;
            align-self: flex-end;
        }

        .you {
            background: #f8d7da;
        }

        .emoji {
            cursor: pointer;
        }

        img.chat-img {
            max-height: 200px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="view_profile.php?user_id=<?php echo $friend_id; ?>" class="btn btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back</a>

    <h4>Chat with <b><?php echo htmlspecialchars($friend['username']); ?></b></h4>

    <div class="chat-box d-flex flex-column mb-3" id="chatBox">
        <!-- Chat messages will load here -->
    </div>

    <form id="chatForm" enctype="multipart/form-data">
        <input type="hidden" name="receiver_id" value="<?php echo $friend_id; ?>">
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Type a message...">
            <input type="file" name="file" class="form-control" style="max-width:200px;">
            <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i></button>
        </div>
    </form>
</div>

<script>
function loadChat() {
    fetch("get_messages.php?user_id=<?php echo $friend_id; ?>")
        .then(res => res.text())
        .then(html => {
            document.getElementById("chatBox").innerHTML = html;
            document.getElementById("chatBox").scrollTop = 9999;
        });
}

document.getElementById("chatForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const form = new FormData(this);
    fetch("send_message.php", {
        method: "POST",
        body: form
    }).then(res => res.text()).then(() => {
        this.reset();
        loadChat();
    });
});

setInterval(loadChat, 3000);
loadChat();
</script>

</body>
</html>
