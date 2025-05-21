<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'DESC';
$valid_sort_columns = ['name', 'created_at'];
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'created_at';
$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM items WHERE name LIKE ?");
$countStmt->bind_param("s", $search_param);
$countStmt->execute();
$countResult = $countStmt->get_result();
$total_items = $countResult->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);

$stmt = $conn->prepare("SELECT * FROM items WHERE name LIKE ? ORDER BY $sort $order LIMIT ? OFFSET ?");
$stmt->bind_param("sii", $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Item List</h2>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name...">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><a href="?search=<?= $search ?>&sort=name&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">Name</a></th>
                <th>Description</th>
                <th>Image</th>
                <th><a href="?search=<?= $search ?>&sort=created_at&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">Created At</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td>
                        <?php if ($item['image']): ?>
                            <img src="../assets/uploads/<?= htmlspecialchars($item['image']) ?>" width="70">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?= $item['created_at'] ?></td>
                    <td>
                        <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_item.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center">
        <?php if ($page > 1): ?>
            <a href="?search=<?= $search ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $page - 1 ?>" class="btn btn-outline-secondary">Previous</a>
        <?php else: ?>
            <span></span>
        <?php endif; ?>

        <span>Page <?= $page ?> of <?= $total_pages ?></span>

        <?php if ($page < $total_pages): ?>
            <a href="?search=<?= $search ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $page + 1 ?>" class="btn btn-outline-secondary">Next</a>
        <?php else: ?>
            <span></span>
        <?php endif; ?>
    </div>

    <br>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>
</body>
</html>
