<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['user'];
if (!in_array($currentUser['role'], ['Admin', 'SuperAdmin'])) {
    echo "Access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];

    if ($newRole === 'SuperAdmin' && $currentUser['role'] !== 'SuperAdmin') {
        $msg = "Only a SuperAdmin can promote someone to SuperAdmin.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $userId);
        $stmt->execute();
        $msg = "Role updated successfully!";
    }
}

if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    $check = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check->bind_param("i", $deleteId);
    $check->execute();
    $roleCheck = $check->get_result()->fetch_assoc();

    if ($deleteId == $currentUser['id']) {
        $msg = "You cannot delete your own account.";
    } elseif ($roleCheck['role'] === 'SuperAdmin') {
        $msg = "You cannot delete a SuperAdmin account.";
    } else {
        $del = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del->bind_param("i", $deleteId);
        $del->execute();
        $msg = "User deleted.";
    }
}

$users = $conn->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Admin Panel – Manage Users</h2>

    <?php if (isset($msg)): ?>
        <div class="alert alert-info"> <?= $msg ?> </div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?php if ($user['role'] === 'SuperAdmin'): ?>
                            <?= $user['role'] ?>
                        <?php else: ?>
                            <form method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" class="form-select me-2">
                                    <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
                                    <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="SuperAdmin" <?= $user['role'] === 'SuperAdmin' ? 'selected' : '' ?>>SuperAdmin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['id'] != $currentUser['id'] && $user['role'] !== 'SuperAdmin'): ?>
                            <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>
</body>
</html>