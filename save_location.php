<?php
// Include DB
include 'db.php';
 
// Sanitize inputs (pro-level security)
$name = $conn->real_escape_string($_POST['name'] ?? '');
$lat = floatval($_POST['lat'] ?? 0);
$lng = floatval($_POST['lng'] ?? 0);
 
if (empty($name) || $lat == 0 || $lng == 0) {
    echo 'Error: Invalid data!';
    exit;
}
 
// Insert (no description for simplicity)
$sql = "INSERT INTO locations (name, lat, lng) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdd", $name, $lat, $lng);
if ($stmt->execute()) {
    echo 'Saved successfully!';
} else {
    echo 'Save failed: ' . $conn->error;
}
$stmt->close();
$conn->close();
?>
