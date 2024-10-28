<?php
// Assuming you have a MySQL database connection
$servername = "localhost";
$username = "dafm5634_ag";
$password = "Ag7us777__";
$dbname = "dafm5634_funrun";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data for table
$sql_data = "SELECT * FROM Funrun WHERE status = 'checked' ORDER BY 'timestamp' ASC";
$result_data = $conn->query($sql_data);

$data = array();

if ($result_data->num_rows > 0) {
    // Fetch associative array
    while ($row = $result_data->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close connection
$conn->close();

// Output data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>