<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Final Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 text-center">
    <h1 class="mb-4">Welcome to the Final Project Web App</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Hello, <?= htmlspecialchars($_SESSION['user']['username']) ?>!</p>
        <a href="pages/dashboard.php" class="btn btn-primary me-2">Go to Dashboard</a>
        <a href="pages/logout.php" class="btn btn-danger">Logout</a>
    <?php else: ?>
        <a href="pages/login.php" class="btn btn-success me-2">Login</a>
        <a href="pages/register.php" class="btn btn-outline-primary">Register</a>
    <?php endif; ?>
</div>
</body>
</html>

