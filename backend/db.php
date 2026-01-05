<?php
$host = "localhost";
$user = "root";
$password = "cscpeboy12";
$dbname = "energyfm_cms";

// creates connection
$conn = new mysqli($host, $user, $password, $dbname);

// checks connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
?>