<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8000');

require 'db.php';

if (!isset($_GET['table'])) {
    echo json_encode(["error" => "Table not specified"]);
    exit;
}

$table = $_GET['table'];
$column = isset($_GET['column']) ? $_GET['column'] : '*';

// validates table and column names
$allowedTables = ['Admin', 'Audio_Broadcast_Log', 'Audio_Broadcast_Song_Entry', 'Category', 'Day_Type', 'DJ_Profile', 'News', 
                    'News_Category', 'Program', 'Program_Anchor_Assignment', 'Program_Day_Type'];
if (!in_array($table, $allowedTables)) {
    echo json_encode(["error" => "Table not allowed"]);
    exit;
}

$sql = $column === '*' ? "SELECT * FROM $table" : "SELECT $column FROM $table";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>