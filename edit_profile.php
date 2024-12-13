<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $profile_picture = $_FILES['profile_picture'];

    // Prepare the query to update profile information
    $query = "UPDATE users SET name = '$name', bio = '$bio'";

    // Handle profile picture update
    if (!empty($profile_picture['name'])) {
        // Generate a unique filename to avoid overwriting existing files
        $file_extension = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '.' . $file_extension;

        // Move the file to the uploads directory
        $upload_path = 'uploads/' . $new_filename;
        move_uploaded_file($profile_picture['tmp_name'], $upload_path);

        // Update the database with the filename (not the full path)
        $query .= ", profile_picture = '$new_filename'";
    }

    $query .= " WHERE id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        echo "Profile updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
