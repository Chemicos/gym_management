<?php
include 'database.php';

$id = $_GET['id'];

// Verifică dacă $id este un număr pentru a preveni SQL injection
if (!is_numeric($id)) {
    echo "Invalid ID";
    exit;
}

$sql = "SELECT * FROM antrenori WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$antrenor = mysqli_fetch_assoc($result);

echo json_encode($antrenor);