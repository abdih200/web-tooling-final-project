<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $desc = htmlspecialchars($_POST['description']);
    $image = $_FILES['image'];

    $target_dir = "../assets/uploads/";
    $filename = time() . "_" . basename($image["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO items (name, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $desc, $filename);
        $stmt->execute();
        $msg = "Item added successfully!";
    } else {
        $msg = "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Add New Item</h2>

    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto">
        <div class="mb-3">
            <label>Item Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Upload Image</label>
            <input type="file" name="image" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Add Item</button>
        <p class="mt-3 text-info text-center"><?= $msg ?></p>
    </form>

    <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>
</body>
</html>

