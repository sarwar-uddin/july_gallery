<?php
include 'includes/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9; // Number of images to load per request
$offset = ($page - 1) * $limit;

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
    GROUP BY images.id
    ORDER BY images.created_at DESC
    LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '
        <div class="col-md-4 col-sm-6">
            <img src="uploads/' . htmlspecialchars($row['filename']) . '" 
                 class="gallery-image lazyload" 
                 data-id="' . htmlspecialchars($row['id']) . '" 
                 data-title="' . htmlspecialchars($row['title']) . '" 
                 data-description="' . htmlspecialchars($row['description']) . '" 
                 data-tags="' . htmlspecialchars($row['tags']) . '"
                 alt="Gallery Image">
        </div>';
    }
} else {
    echo ''; // No more images to load
}
?>
