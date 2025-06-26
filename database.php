<?php

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "timetable";


$conn = new mysqli($servername, $username, $password,$dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "CREATE DATABASE IF NOT EXISTS timetable";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}


$conn->select_db($dbname);


$table_sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    registration_number VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'students' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}


$conn->close();
?>
