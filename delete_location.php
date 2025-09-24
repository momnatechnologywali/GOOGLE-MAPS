<?php
// Include DB
include 'db.php';
 
$id = intval($_POST['id'] ?? 0);
 
if ($id <= 0) {
    echo 'Error: Invalid ID!';
    exit;
}
 
$sql = "DELETE FROM locations WHERE id = ? AND user_id = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo 'Deleted successfully!';
} else {
    echo 'Delete failed: ' . $conn->error;
}
$stmt->close();
$conn->close();
?>
