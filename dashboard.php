<?php
session_start();

// Check if the user is logged in and has the correct role (admin or moderator)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'moderator')) {
    header('Location: login.php');  // Redirect to login if not logged in or not an admin/moderator
    exit;
}

// Include the database connection
include 'includes/db.php';

// Fetch the total number of users
$sql = "SELECT COUNT(*) AS total_users FROM users";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$total_users = $row['total_users'];

// Fetch the total number of images pending approval
$sql = "SELECT COUNT(*) AS pending_images FROM images WHERE is_approved = 0";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$pending_images = $row['pending_images'];

// Fetch the total number of approved images
$sql = "SELECT COUNT(*) AS approved_images FROM images WHERE is_approved = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$approved_images = $row['approved_images'];

// Fetch recent activity (last 5 activities)
$sql = "SELECT c.comment, c.created_at, u.username, i.title 
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN images i ON c.image_id = i.id
        ORDER BY c.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
$recent_activity = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recent_activity[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <?php include 'includes/header.php'; // Include header 
    ?>

    <div class="container dashboard-container">
        <div class="dashboard-header">
            Admin Dashboard
        </div>

        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

        <!-- Stats Section -->
        <div class="stats-cards">
            <div class="stats-card">
                <h3><?php echo $total_users; ?></h3>
                <p>Users</p>
            </div>
            <div class="stats-card">
                <h3><?php echo $pending_images; ?></h3>
                <p>Images Pending Approval</p>
            </div>
            <div class="stats-card">
                <h3><?php echo $approved_images; ?></h3>
                <p>Approved Images</p>
            </div>
        </div>

        <!-- Dashboard Links Section -->
        <div class="row mb-4">
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <div class="col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Manage Users</h5>
                            <p class="card-text">Add, edit, or delete user accounts.</p>
                            <a href="manage_users.php" class="btn btn-primary">Go to Manage Users</a>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'moderator') { ?>
                <div class="col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Manage Images</h5>
                            <p class="card-text">Approve, edit, or delete image uploads.</p>
                            <a href="manage_images.php" class="btn btn-primary">Go to Manage Images</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>


        <!-- Recent Activity Section -->
        <div class="recent-activity">
            <h3>Recent Activity</h3>
            <?php foreach ($recent_activity as $activity) { ?>
                <div class="activity-item">
                    <strong><?php echo htmlspecialchars($activity['username']); ?></strong> commented on the image titled
                    <em>"<?php echo htmlspecialchars($activity['title']); ?>"</em>: <?php echo htmlspecialchars($activity['comment']); ?>
                    <br>
                    <small>On <?php echo date('F j, Y, g:i a', strtotime($activity['created_at'])); ?></small>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>