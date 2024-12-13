<?php
include 'includes/db.php';

// Check if there's a search query
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Construct the SQL query with a search filter if necessary
$sql = "SELECT 
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
WHERE images.is_approved = 1";

if (!empty($searchQuery)) {
    // Add search condition to the query if a search term is provided
    $sql .= " AND (images.title LIKE '%$searchQuery%' OR images.description LIKE '%$searchQuery%' OR tags.name LIKE '%$searchQuery%')";
}

$sql .= " GROUP BY images.id ORDER BY images.created_at DESC";

// Execute the query
$result = mysqli_query($conn, $sql);

// Check if images exist
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
    echo '<p class="text-center">No images available.</p>';
}
?>
