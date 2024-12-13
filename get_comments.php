<?php
include 'includes/db.php';

$image_id = $_GET['image_id'];
$query = "SELECT c.comment, c.created_at, u.username 
          FROM comments c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.image_id = ? 
          ORDER BY c.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $image_id);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
while ($row = $result->fetch_assoc()) {
    $output .= '<div class="comment mb-2">';
    $output .= '<strong>' . htmlspecialchars($row['username']) . '</strong>: ';
    $output .= htmlspecialchars($row['comment']);
    $output .= '<br><small class="text-muted">' . $row['created_at'] . '</small>';
    $output .= '</div>';
}

echo $output ?: '<p>No comments yet. Be the first to comment!</p>';
?>
