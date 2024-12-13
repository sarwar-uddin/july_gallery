<?php
include 'includes/db.php'; // Database connection
session_start();

// Check if user ID is passed via GET request
if (!isset($_GET['user_id'])) {
    echo "<p>User not found.</p>";
    exit();
}

$user_id = $_GET['user_id'];

// Fetch user details
$query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    echo "<p>User not found.</p>";
    exit();
}

// Fetch approved images uploaded by the user
$image_query = "SELECT images.id, images.filename, images.title, images.description, 
                GROUP_CONCAT(tags.name ORDER BY tags.name ASC) AS tags
                FROM images
                LEFT JOIN image_tags ON images.id = image_tags.image_id
                LEFT JOIN tags ON image_tags.tag_id = tags.id
                WHERE images.is_approved = 1 AND images.user_id = '$user_id'
                GROUP BY images.id
                ORDER BY images.created_at DESC";
$image_result = mysqli_query($conn, $image_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= htmlspecialchars($user['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
    const isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>
</head>

<body>
    <?php include 'includes/header.php'; // Include header 
    ?>

    <div class="container page-container">
        <div class="profile-header">
            <img src="uploads/<?= htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <h1><?= htmlspecialchars($user['name']); ?></h1>
            <p><?= htmlspecialchars($user['bio']); ?></p>

            <!-- Display Edit Button if the user is logged in and viewing their own profile -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']) : ?>
                <button class="btn btn-primary edit-button" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
            <?php endif; ?>

        </div>

        <div class="gallery-container">
        <h2>Uploaded Images</h2>
        <div class="row g-4">
            <?php
            if (mysqli_num_rows($image_result) > 0) {
                while ($image = mysqli_fetch_assoc($image_result)) {
                    $image_url = 'uploads/thumbnails/' . $image['filename'];
                    echo '
                    <div class="col-md-4 col-sm-6">
                        <img src="' . $image_url . '" class="gallery-image" data-id="' . $image['id'] . '" data-title="' . htmlspecialchars($image['title']) . '" data-description="' . htmlspecialchars($image['description']) . '" />
                    </div>
                ';
                }
            } else {
                echo '<p class="text-center">No images uploaded by this user.</p>';
            }
            ?>
        </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($user['bio']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Details Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <span class="modal-close" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i>
                </span>

                <div class="modal-image-container">
                    <img id="modalImage" class="modal-image" src="" alt="Modal Image">
                </div>
                <div class="modal-info">

                    <h5 class="modal-title" id="imageModalLabel"></h5>

                    <div class="modal-meta d-flex align-items-center">
                        <!-- Date Icon and Date -->
                        <div class="modal-meta-item d-flex align-items-center me-4">
                            <i class="bi bi-calendar-event me-2"></i>
                            <span id="modalEventDate"></span>
                        </div>

                        <!-- Uploader Icon and Username -->
                        <div class="modal-meta-item d-flex align-items-center">
                            <i class="bi bi-person-circle me-2"></i>
                            <span id="modalUploadedBy"></span>
                        </div>
                    </div>

                    <div id="modalTags" class="modal-tags"></div>
                    <div id="modalDescription" class="modal-description"></div>




                    <div class="modal-stats">
                        <button id="likeButton">
                            <i id="likeIcon" class="bi bi-heart"></i> <span id="modalLikes">0</span>
                        </button>
                    </div>

                    <div id="notification" class="toast align-items-center text-white bg-danger border-0 position-fixed bottom-0 end-0 p-2 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1055; display: none;">
                        <div class="d-flex">
                            <div class="toast-body">
                                You need to log in to like images.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>


                    <div class="modal-comments">
                        <h6>Comments</h6>
                        <div id="commentsList" class="mb-3">
                            <!-- Comments will be dynamically loaded here -->
                        </div>
                        <?php if (isset($_SESSION['user_id'])) : ?>
                            <form id="commentForm">
                                <input type="hidden" id="imageIdInput" name="image_id">
                                <textarea class="form-control mb-2" id="commentInput" name="comment" rows="2" placeholder="Write a comment..." required></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                            </form>
                        <?php else : ?>
                            <p>Please <a href="login.php">log in</a> to comment.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <div class="modal-nav left">
                <i class="bi bi-chevron-left"></i>
            </div>
            <div class="modal-nav right">
                <i class="bi bi-chevron-right"></i>
            </div>

        </div>
    </div>

    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/gallery.js"></script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>