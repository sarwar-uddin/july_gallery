<?php
include 'includes/db.php';

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $query = "SELECT 
                images.id, 
                images.filename, 
                images.title
              FROM images
              WHERE images.is_approved = 1
              AND images.featured = 0
              AND images.title LIKE '%$search%' 
              ORDER BY images.created_at DESC";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '
                <div class="col-md-4 col-sm-6">
                    <a href="image.php?id=' . $row['id'] . '">
                        <img src="uploads/thumbnails/' . htmlspecialchars($row['filename']) . '" 
                             class="gallery-image lazyload" 
                             data-id="' . htmlspecialchars($row['id']) . '" 
                             alt="Gallery Image">
                        <h5>' . htmlspecialchars($row['title']) . '</h5>
                    </a>
                </div>';
        }
    } else {
        echo '<p class="text-center">No results found for "' . htmlspecialchars($search) . '".</p>';
    }
}
?>
