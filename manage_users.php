<?php
include 'includes/db.php';
session_start();

// Ensure only admin has access to this page
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Handle updating user roles or deleting a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'updateRole') {
        $user_id = $_POST['user_id'];
        $new_role = $_POST['new_role'];
        $update_query = "UPDATE users SET role = '$new_role' WHERE id = $user_id";
        mysqli_query($conn, $update_query);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'deleteUser') {
        $user_id = $_POST['user_id'];
        $delete_query = "DELETE FROM users WHERE id = $user_id";
        mysqli_query($conn, $delete_query);
    }
}

// Fetch all users for display
$query = "SELECT id, username, email, role, created_at, profile_picture, name, bio FROM users";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="assets/css/style.css">

    <script>
        $(document).ready(function() {
            // Live Search
            $('#searchInput').on('keyup', function() {
                let searchTerm = $(this).val().toLowerCase();
                $('.user-row').each(function() {
                    let username = $(this).find('.username').text().toLowerCase();
                    if (username.indexOf(searchTerm) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Update user role
            $('.updateRole').on('change', function() {
                let user_id = $(this).data('user-id');
                let new_role = $(this).val();
                $.post('manage_users.php', {
                    action: 'updateRole',
                    user_id: user_id,
                    new_role: new_role
                });
            });

            // Delete user
            $('.deleteUser').on('click', function() {
                let user_id = $(this).data('user-id');
                if (confirm('Are you sure you want to delete this user?')) {
                    $.post('manage_users.php', {
                        action: 'deleteUser',
                        user_id: user_id
                    }, function() {
                        location.reload();
                    });
                }
            });
        });
    </script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Manage Users</h1>

        <div class="search-bar mb-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for users by username...">
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="user-row">
                        <td class="username"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <select class="form-control updateRole" data-user-id="<?php echo $row['id']; ?>">
                                <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                <option value="member" <?php if ($row['role'] == 'member') echo 'selected'; ?>>member</option>
                                <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                            </select>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-danger deleteUser" data-user-id="<?php echo $row['id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
