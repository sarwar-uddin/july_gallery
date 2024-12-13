<?php
include 'includes/db.php';
session_start();

// Get the action parameter to differentiate between different data requests
$action = $_GET['action'] ?? null;
$imageId = isset($_GET['image_id']) ? intval($_GET['image_id']) : 0;

if ($action && $imageId > 0) {
    switch ($action) {
        case 'details':
            // Fetch image details, username, user_id, and formatted event date
            $query = "
                SELECT images.*, users.username, users.id AS user_id, DATE_FORMAT(images.event_date, '%d %M, %Y') AS formatted_event_date
                FROM images
                JOIN users ON images.user_id = users.id
                WHERE images.id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $imageId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode([
                    'title' => $row['title'],
                    'filename' => $row['filename'],
                    'description' => $row['description'],
                    'username' => $row['username'],
                    'user_id' => $row['user_id'],
                    'event_date' => $row['formatted_event_date']
                ]);
            } else {
                echo json_encode(['error' => 'Image not found']);
            }
            $stmt->close(); 
            break; 
         

        case 'stats':
            $userId = $_SESSION['user_id'] ?? 0;

            // Fetch views and likes
            $query = "SELECT views FROM images WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $imageId);
            $stmt->execute();
            $stmt->bind_result($views);
            $stmt->fetch();
            $stmt->close();

            $likesQuery = "SELECT COUNT(*) AS likes FROM likes WHERE image_id = ?";
            $stmt = $conn->prepare($likesQuery);
            $stmt->bind_param("i", $imageId);
            $stmt->execute();
            $stmt->bind_result($likes);
            $stmt->fetch();
            $stmt->close();

            $userLiked = false;
            if ($userId > 0) {
                $likeQuery = "SELECT 1 FROM likes WHERE user_id = ? AND image_id = ?";
                $stmt = $conn->prepare($likeQuery);
                $stmt->bind_param("ii", $userId, $imageId);
                $stmt->execute();
                $stmt->store_result();
                $userLiked = $stmt->num_rows > 0;
                $stmt->close();
            }

            echo json_encode(['views' => $views, 'likes' => $likes, 'userLiked' => $userLiked]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['error' => 'Invalid parameters']);
}
?>
