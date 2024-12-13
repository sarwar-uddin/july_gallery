<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'moderator')) {
    echo "You do not have permission to perform this action.";
    exit;
}

if (isset($_GET['action'], $_GET['image_id'])) {
    $image_id = $_GET['image_id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        // Approve image
        $query = "UPDATE images SET is_approved = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        if ($stmt->execute()) {
            echo "Image approved successfully.";
        } else {
            echo "Failed to approve the image.";
        }
    } elseif ($action == 'unapprove') {
        // Unapprove image
        $query = "UPDATE images SET is_approved = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        if ($stmt->execute()) {
            echo "Image unapproved successfully.";
        } else {
            echo "Failed to unapprove the image.";
        }
    } elseif ($action == 'delete') {
        // Delete image
        $query = "SELECT filename FROM images WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $stmt->bind_result($filename);
        $stmt->fetch();
        $stmt->close();

        // Delete file from the server
        if (file_exists("uploads/{$filename}")) {
            unlink("uploads/{$filename}");
        }

        // Delete the image record
        $query = "DELETE FROM images WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        if ($stmt->execute()) {
            echo "Image deleted successfully.";
        } else {
            echo "Failed to delete the image.";
        }
    }

    $stmt->close();
}

$conn->close();
?>
