<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageId = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $userId = $_SESSION['user_id'] ?? 0;

    if ($userId === 0) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to like an image.']);
        exit;
    }

    if ($imageId > 0) {
        if ($action === 'like') {
            $checkQuery = "SELECT 1 FROM likes WHERE user_id = ? AND image_id = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param("ii", $userId, $imageId);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                $stmt->close();
                $insertQuery = "INSERT INTO likes (user_id, image_id) VALUES (?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ii", $userId, $imageId);
                $stmt->execute();
                $stmt->close();

                $updateQuery = "UPDATE images SET likes = likes + 1 WHERE id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("i", $imageId);
                $stmt->execute();
                $stmt->close();
                echo json_encode(['status' => 'liked']);
            } else {
                echo json_encode(['status' => 'already_liked']);
            }
        } elseif ($action === 'unlike') {
            $deleteQuery = "DELETE FROM likes WHERE user_id = ? AND image_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("ii", $userId, $imageId);
            $stmt->execute();
            $stmt->close();

            $updateQuery = "UPDATE images SET likes = likes - 1 WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("i", $imageId);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['status' => 'unliked']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid image ID']);
    }
}
?>
