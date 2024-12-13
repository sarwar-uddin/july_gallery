<?php
include 'includes/db.php';
session_start();

// Fetch the featured image
$featuredQuery = "SELECT id, filename FROM images WHERE is_approved = 1 AND featured = 0 LIMIT 1";
$featuredResult = mysqli_query($conn, $featuredQuery);
$featuredImage = mysqli_fetch_assoc($featuredResult);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>July Gallery</title>
    <meta name="description" content="Explore the July Revolution Gallery, showcasing powerful images of protests, martyrs, and resistance. Discover a curated collection of impactful visuals categorized for easy navigation, with quick views and detailed descriptions. Immerse yourself in history through art.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=0.1">
    <script>
        const isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>


</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container page-container">

        <!-- Header Section -->
        <div class="row align-items-center">
            <!-- Left Section: Logo, Title, and Search Bar -->
            <div class="col-md-6 text-center text-md-start">
                <div class="logo-container mb-3">
                    <img src="assets/images/Abu_Sayed_by_Kousik_Sarkar.webp" width="150px" height="150px" alt="Logo" class="logo img-fluid">
                </div>
                <h1 class="gallery-title">July Revolution Gallery</h1>
                <div class="search-bar mt-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search for images...">
                </div>
            </div>
            <!-- Right Section: Featured Image -->

        </div>

        <hr />

        <div class="gallery-container">
            <div class="row g-4">
                <?php
                $query = "SELECT 
           images.id, 
           images.filename, 
           images.title, 
           images.description, 
           images.event_date, 
           images.created_at AS upload_date, 
           images.views, 
           images.is_own_work, 
           images.is_nsfw, 
           images.is_approved,
           categories.name AS category, 
           GROUP_CONCAT(tags.name ORDER BY tags.name ASC) AS tags
         FROM images
         LEFT JOIN image_tags ON images.id = image_tags.image_id
         LEFT JOIN tags ON image_tags.tag_id = tags.id
         LEFT JOIN categories ON images.category_id = categories.id
         WHERE images.is_approved = 1
         AND images.featured=0
         GROUP BY images.id
         ORDER BY images.created_at DESC";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '
                    <div class="col-md-4 col-sm-6">
                        <img src="uploads/thumbnails/' . htmlspecialchars($row['filename']) . '" 
                             class="gallery-image lazyload" 
                             data-id="' . htmlspecialchars($row['id']) . '" 
                             data-title="' . htmlspecialchars($row['title']) . '" 
                             data-description="' . htmlspecialchars($row['description']) . '" 
                             data-tags="' . htmlspecialchars($row['tags']) . '"
                             alt="Gallery Image">
                    </div>';
                    }
                } else {
                    echo '<p class="text-center">No images available.</p>';
                }
                ?>
            </div>
            <div>
            </div>

            <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <span class="modal-close" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                        </span>
                        <div class="modal-nav left">
                            <i class="bi bi-chevron-left"></i>
                        </div>
                        <div class="modal-nav right">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <div class="modal-image-container">
                            <img id="modalImage" data-image-id="" class="modal-image" src="" alt="Modal Image">
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

                                <!-- Comment Button -->
                                <button id="commentButton" class="btn btn-light me-3">
                                    <i class="bi bi-chat-dots"></i>
                                </button>

                                <div class="dropdown">
                                    <button id="shareButton" class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-share"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item" id="shareFacebook">
                                                <i class="bi bi-facebook"></i> Facebook
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" id="shareTwitter">
                                                <i class="bi bi-twitter"></i> Twitter
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" id="shareWhatsapp">
                                                <i class="bi bi-whatsapp"></i> WhatsApp
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" id="copyLink">
                                                <i class="bi bi-link-45deg"></i> Copy Link
                                            </button>
                                        </li>
                                    </ul>
                                </div>

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


                </div>
            </div>


            <script src="assets/js/gallery.js"></script>
            <script src="assets/js/scripts.js"></script>
</body>

</html>