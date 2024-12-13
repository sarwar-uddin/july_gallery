<?php
include 'includes/db.php';
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid Image ID.');
}

$imageId = intval($_GET['id']);

// Fetch image details
$query = "SELECT 
    images.id, 
    images.filename, 
    images.title, 
    images.description, 
    images.event_date, 
    images.created_at AS upload_date, 
    images.is_own_work, 
    images.is_nsfw, 
    categories.name AS category, 
    GROUP_CONCAT(tags.name ORDER BY tags.name ASC) AS tags
FROM images
LEFT JOIN image_tags ON images.id = image_tags.image_id
LEFT JOIN tags ON image_tags.tag_id = tags.id
LEFT JOIN categories ON images.category_id = categories.id
WHERE images.id = $imageId AND images.is_approved = 1
GROUP BY images.id";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die('Image not found.');
}

$image = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($image['title']); ?> - Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <img src="uploads/<?php echo htmlspecialchars($image['filename']); ?>"
                    class="img-fluid"
                    alt="<?php echo htmlspecialchars($image['title']); ?>">
            </div>
            <div class="col-lg-4">
                <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                <p class="text-muted">
                    <i class="bi bi-calendar-event"></i>
                    <?php echo date('F d, Y', strtotime($image['event_date'])); ?>
                </p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($image['description']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($image['category']); ?></p>
                <p><strong>Tags:</strong> <?php echo htmlspecialchars($image['tags']); ?></p>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button id="shareButton" class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-share"></i> Share
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
                                <button class="dropdown-item" id="copyLink">
                                    <i class="bi bi-link-45deg"></i> Copy Link
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Share button functionality
        document.getElementById('copyLink').addEventListener('click', function() {
            const link = window.location.href;
            navigator.clipboard.writeText(link).then(() => {
                alert('Link copied to clipboard!');
            });
        });
    </script>
</body>

</html>