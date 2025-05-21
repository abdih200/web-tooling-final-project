<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Welcome, <?= htmlspecialchars($user['username']) ?> (<?= $user['role'] ?>)</h2>

    <div class="mb-4">
        <a href="add_item.php" class="btn btn-success me-2">+ Add New Item</a>
        <a href="view_items.php" class="btn btn-primary me-2">View All Items</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if ($user['role'] === 'Admin' || $user['role'] === 'SuperAdmin'): ?>
        <div class="alert alert-info">
            You have access to the <a href="admin_panel.php" class="alert-link">Admin Panel</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>