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

$stmt = $conn->prepare("SELECT image FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if ($item) {
    $image_path = "../assets/uploads/" . $item['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: view_items.php");
exit();
