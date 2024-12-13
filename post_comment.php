<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'Unauthorized';
    exit;
}

$user_id = $_SESSION['user_id'];
$image_id = $_POST['image_id'];
$comment = trim($_POST['comment']);

if ($comment) {
    $query = "INSERT INTO comments (image_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $image_id, $user_id, $comment);
    if ($stmt->execute()) {
        echo 'Comment added';
    } else {
        echo 'Error adding comment';
    }
} else {
    echo 'Comment cannot be empty';
}
?>
