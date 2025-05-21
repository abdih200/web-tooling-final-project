<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid item ID.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found.";
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $desc = htmlspecialchars($_POST['description']);

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/uploads/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $filename = $item['image'];
    }

    $stmt = $conn->prepare("UPDATE items SET name = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $desc, $filename, $id);
    $stmt->execute();

    $msg = "Item updated successfully!";
    $item['name'] = $name;
    $item['description'] = $desc;
    $item['image'] = $filename;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Edit Item</h2>
    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto">
        <div class="mb-3">
            <label>Item Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($item['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <p>Current Image:</p>
            <img src="../assets/uploads/<?= $item['image'] ?>" width="100" class="mb-2">
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-warning w-100">Update</button>
        <p class="mt-3 text-info text-center"><?= $msg ?></p>
    </form>

    <a href="view_items.php" class="btn btn-secondary mt-4">Back</a>
</div>
</body>
</html>
