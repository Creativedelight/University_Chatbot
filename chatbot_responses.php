<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "timetable";


$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql_create_table = "CREATE TABLE IF NOT EXISTS chatbot_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_input VARCHAR(255) NOT NULL,
    bot_response TEXT NOT NULL
)";

if (!mysqli_query($conn, $sql_create_table)) {
    die("Error creating table: " . mysqli_error($conn));
}

$sql_insert = "INSERT INTO chatbot_responses (user_input, bot_response) VALUES
('hello', 'I am doing fine! How can I assist you today? Here are the available services: 
1. Find venue for your unit
2. Find the time your unit will be held
3. Find occupied rooms
4. Find unoccupied/free rooms
5. Know which lecturer is teaching a particular unit
6. Know when a lecturer is teaching and the venue
7. Get a simplified timetable for your intake
Please enter the number of the service you need.')";

// Execute insertion only if 'hello' response does not exist
$check_query = "SELECT * FROM chatbot_responses WHERE user_input = 'hello'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) == 0) {
    if (!mysqli_query($conn, $sql_insert)) {
        die("Error inserting data: " . mysqli_error($conn));
    }
}

echo "Table created and data inserted successfully.";

mysqli_close($conn);
?>
