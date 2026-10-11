<?php
$host = "localhost";
$user = "root";
$password = ""; // በ XAMPP ፓስወርድ ባዶ ነው
$dbname = "aquila_db";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>