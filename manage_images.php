<?php
session_start(); // Start the session

// Include your database connection
require_once 'includes/db.php';

// Check if the user is logged in and has the correct role (admin or moderator)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'moderator')) {
    header('Location: login.php');  // Redirect to login if not logged in or not an admin/moderator
    exit;
}

// Handle search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch all images (approved and unapproved), ordering by approval status (unapproved first)
$query = "SELECT id, title, filename, is_approved, featured FROM images WHERE title LIKE ? ORDER BY is_approved ASC, id DESC";
$stmt = $conn->prepare($query);
$search_param = "%$search_query%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Images</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center">Manage Images</h1>

        <!-- Search Bar -->
        <form id="search-form" class="mb-4">
            <div class="input-group">
                <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by title" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <!-- Spinner for AJAX request -->
        <div id="loading-spinner" class="text-center" style="display:none;">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <?php if ($result->num_rows === 0) : ?>
            <div class='alert alert-info' role='alert'>No images found.</div>
        <?php endif; ?>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="image-table">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><a href="image.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></td>
                        <td>
                            <?php if ($row['is_approved'] == 1): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif ($row['is_approved'] == 0): ?>
                                <span class="badge bg-warning">Unapproved</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['featured'] == 1): ?>
                                <button class="btn btn-warning btn-sm unfeature-btn" data-id="<?= $row['id'] ?>">Unfeature</button>
                                <?php else: ?>
                                <button class="btn btn-success btn-sm feature-btn" data-id="<?= $row['id'] ?>">Feature</button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['is_approved'] == 0): ?>
                                <button class="btn btn-success btn-sm approve-btn" data-id="<?= $row['id'] ?>">Approve</button>
                            <?php elseif ($row['is_approved'] == 1): ?>
                                <button class="btn btn-warning btn-sm unapprove-btn" data-id="<?= $row['id'] ?>">Unapprove</button>
                            <?php endif; ?>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        // Handle image actions via AJAX
        $(document).on('click', '.approve-btn', function() {
            var imageId = $(this).data('id');
            $.ajax({
                url: 'manage_images.php',
                method: 'POST',
                data: {
                    action: 'approve',
                    image_id: imageId
                },
                success: function(response) {
                    location.reload();
                }
            });
        });

        $(document).on('click', '.unapprove-btn', function() {
            var imageId = $(this).data('id');
            $.ajax({
                url: 'manage_images.php',
                method: 'POST',
                data: {
                    action: 'unapprove',
                    image_id: imageId
                },
                success: function(response) {
                    location.reload();
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            var imageId = $(this).data('id');
            if (confirm("Are you sure you want to delete this image?")) {
                $.ajax({
                    url: 'manage_images.php',
                    method: 'POST',
                    data: {
                        action: 'delete',
                        image_id: imageId
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        });

        // Feature/Unfeature image via AJAX
        $(document).on('click', '.feature-btn', function() {
            var imageId = $(this).data('id');
            $.ajax({
                url: 'manage_images.php',
                method: 'POST',
                data: {
                    action: 'feature',
                    image_id: imageId
                },
                success: function(response) {
                    location.reload();
                }
            });
        });

        //Unfeature image via ajax
        $(document).on('click', '.unfeature-btn', function() {
            var imageId = $(this).data('id');
            $.ajax({
                url: 'manage_images.php',
                method: 'POST',
                data: {
                    action: 'unfeature',
                    image_id:imageId
                },
                success: function(response) {
                    location.reload();
                }
            });
        });

        // Live search via AJAX
        $("#search-input").on("input", function() {
            var searchQuery = $(this).val();
            $.ajax({
                url: 'manage_images.php',
                method: 'GET',
                data: {
                    search: searchQuery
                },
                success: function(response) {
                    $("#loading-spinner").hide();
                    $("#image-table").html($(response).find("#image-table").html());
                }
            });
        });

        // Handle the form submission for search (AJAX)
        $("#search-form").submit(function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>

<?php
// Handle image approval, unapproval, and deletion actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['image_id'])) {
    $image_id = $_POST['image_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Approve the image
        $query = "UPDATE images SET is_approved = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        echo "Image has been approved.";
    }

    if ($action === 'unapprove') {
        // Unapprove the image
        $query = "UPDATE images SET is_approved = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        echo "Image has been unapproved.";
    }

    if ($action === 'delete') {
        // Delete the image from database and storage
        $query = "SELECT filename FROM images WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $stmt->bind_result($filename);
        $stmt->fetch();
        $stmt->close();

        if (file_exists("uploads/{$filename}")) {
            unlink("uploads/{$filename}"); // Delete the file from the server
        }

        // Delete the image record
        $query = "DELETE FROM images WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        echo "Image has been deleted.";
    }

    if ($action === 'feature') {

        // Set the selected image as featured
        $query = "UPDATE images SET featured = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();

        echo "Image has been featured.";
    }

    if ($action === 'unfeature') {
        //unfeature the image
        $query = "UPDATE images SET featured = 0 where id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();

        echo"Image has been unfeatured.";
    }
}
?>
