<?php
// Include DB
include 'db.php';
 
$sql = "SELECT name, lat, lng, description FROM locations WHERE user_id = 1 ORDER BY created_at DESC";
$result = $conn->query($sql);
 
$locations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
}
echo json_encode($locations);
 
$conn->close();
?>
