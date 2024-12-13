<?php
include 'includes/db.php';
session_start();

// Fetch the featured image
$featuredQuery = "SELECT id, filename FROM images WHERE is_approved = 1 AND featured = 1 LIMIT 1";
$featuredResult = mysqli_query($conn, $featuredQuery);
$featuredImage = mysqli_fetch_assoc($featuredResult);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container text-center mt-5">
        <h1>Subscribe to see Featured Contents</h1>
        <p>You can enjoy our featured content for Tk2000 only.</p>
        <a href="checkout.php?price=2000" class="btn btn-primary">Become a member now</a>
    </div>     
        
</body>

</html>