<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'includes/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch categories for dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

// Fetch tags for checkbox selection
$tags_query = "SELECT * FROM tags";
$tags_result = mysqli_query($conn, $tags_query);

// Initialize success message
$success_message = "";

function createThumbnail($source, $destination, $maxWidth, $maxHeight)
{
    // Get image dimensions and type
    list($originalWidth, $originalHeight, $imageType) = getimagesize($source);

    // Calculate aspect ratio
    $aspectRatio = $originalWidth / $originalHeight;

    if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
        if ($originalWidth / $maxWidth > $originalHeight / $maxHeight) {
            $newWidth = (int) round($maxWidth);
            $newHeight = (int) round($maxWidth / $aspectRatio);
        } else {
            $newHeight = (int) round($maxHeight);
            $newWidth = (int) round($maxHeight * $aspectRatio);
        }
         
    } else {
        // If the image is smaller than the max dimensions, keep its original size
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    }

    // Create a blank canvas for the thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

    // Load the original image based on type
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($source);
            break;
        default:
            return false; // Unsupported image type
    }

    // Copy and resize the original image into the thumbnail
    imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Save the thumbnail to the destination path
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumbnail, $destination, 90); // 90 is the quality
            break;
        case IMAGETYPE_PNG:
            imagepng($thumbnail, $destination);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumbnail, $destination);
            break;
    }

    // Free memory
    imagedestroy($thumbnail);
    imagedestroy($sourceImage);

    return true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim(mysqli_real_escape_string($conn, $_POST['title']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $category_id = $_POST['category_id'];
    $event_date = $_POST['event_date'];
    $is_nsfw = isset($_POST['is_nsfw']) ? 1 : 0;

    // Validate form fields
    if (empty($title) || empty($description) || empty($category_id) || empty($event_date) || !isset($_FILES['image'])) {
        $error = "All fields are mandatory. Please fill in all fields.";
    } else {
        // Handle file upload
        if ($_FILES['image']['error'] == 0) {
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_name = time() . "_" . basename($_FILES['image']['name']);
            $image_path = 'uploads/' . $image_name;

            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Generate thumbnail
                $thumbnail_path = 'uploads/thumbnails/' . $image_name; // Save thumbnails in a separate directory
                if (!file_exists('uploads/thumbnails')) {
                    mkdir('uploads/thumbnails', 0777, true); // Create the directory if it doesn't exist
                }

                // Call the thumbnail function
                if (createThumbnail($image_path, $thumbnail_path, 380, 285)) {
                    $escaped_image_name = mysqli_real_escape_string($conn, $image_name);

                    // Insert image data into the database
                    $insert_query = "INSERT INTO images (user_id, filename, title, description, category_id, event_date, is_nsfw) 
                                     VALUES ('$user_id', '$escaped_image_name', '$title', '$description', '$category_id', '$event_date', '$is_nsfw')";
                    if (mysqli_query($conn, $insert_query)) {
                        $image_id = mysqli_insert_id($conn);

                        // Display success message
                        $success_message = "Upload successful! Thumbnail created. An admin or moderator will review the image soon.";
                    } else {
                        $error = "Error inserting image data into the database.";
                    }
                } else {
                    $error = "Failed to create a thumbnail.";
                }
            } else {
                $error = "Failed to upload the image file.";
            }
        } else {
            $error = "No valid image file selected.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit an Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-4">
        <h1>Upload Image</h1>

        <!-- Success Message -->
        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success">
                <?= $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form action="upload_image.php" method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3">
                <label for="title" class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category:</label>
                <select name="category_id" class="form-select" required>
                    <option value="" disabled selected>Select a category</option>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)) : ?>
                        <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="event_date" class="form-label">Event Date:</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="is_nsfw" class="form-check-label">NSFW Content:</label>
                <input type="checkbox" name="is_nsfw" value="1" class="form-check-input">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Choose Image:</label>
                <input type="file" name="image" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload Image</button>
        </form>
    </div>

    <?php require_once 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>