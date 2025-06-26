<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
require 'vendor/autoload.php'; 

            use PhpOffice\PhpSpreadsheet\Spreadsheet;
            use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$conn = new mysqli("localhost", "root", "", "timetable");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['service_step'])) {
    $_SESSION['service_step'] = null;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $userMessage = strtolower(trim($_POST['message']));
    $response = "";

    if ($userMessage == "0") {
    $_SESSION['service_step'] = null;
    $response = "
    <div class='bot-message'>
        <p><b>👋 You are back on the main menu select another service</b></p>
        <p></p>
        <ul class='menu-list'>
            <li>1️⃣ <b>Find venue</b> for your unit</li>
            <li>2️⃣ <b>Find the time</b> your unit will be held</li>
            <li>3️⃣ <b>Find occupied rooms</b></li>
            <li>4️⃣ <b>Find unoccupied/free rooms</b></li>
            <li>5️⃣ <b>Know which lecturer</b> is teaching a particular unit</li>
            <li>6️⃣ <b>Get a simplified timetable</b> for your intake</li>
        </ul>
        <p><b>🔄 Type 0 to return to this menu at any time.</b></p>
    </div>";
}

    
    elseif ($_SESSION['service_step'] === null) {
        $greetings = ["hello", "hi", "hey", "good morning", "good afternoon", "good evening"];
        if (in_array($userMessage, $greetings)) {
            $response = "Hello! How can I assist you today? 😊<br>Select a service by entering a number:<br>";
            $response .= "1. Find venue for your unit<br>";
            $response .= "2. Find the time your unit will be held<br>";
            $response .= "3. Find occupied rooms<br>";
            $response .= "4. Find unoccupied/free rooms<br>";
            $response .= "5. Know which lecturer is teaching a particular unit<br>";
            $response .= "6. Get a simplified timetable for your intake<br>";
            $response .= "<br><b>Type 0 to return to this menu at any time.</b>";
        } elseif ($userMessage == "1") {
            $_SESSION['service_step'] = "waiting_for_unit";
            $response = "Please enter the unit code:<br><br><b>Type 0 to return to home.</b>";
        } elseif ($userMessage == "2") {
            $_SESSION['service_step'] = "waiting_for_unit_time";
            $response = "Please enter the unit code:<br><br><b>Type 0 to return to home.</b>";
        } elseif ($userMessage == "3") {
            $_SESSION['service_step'] = "waiting_for_day";
            $response = "Please enter a day to check occupied rooms (e.g., Monday, Tuesday, etc.):<br><br><b>Type 0 to return to home.</b>";
        } elseif ($userMessage == "4") {
            $_SESSION['service_step'] = "waiting_for_unoccupied_rooms";
            $response = "Please enter the day(s) to check unoccupied rooms (e.g., Monday, Tuesday, etc.):<br>You can enter multiple days separated by commas.<br><br><b>Type 0 to return to home.</b>";
        } elseif ($userMessage === "5") { 
            $_SESSION['service_step'] = "waiting_for_lecturer";
            $response = "Please enter the unit code to find out which lecturer is teaching it:<br><br><b>Type 0 to return to home.</b>";
        }elseif ($userMessage === "6") { 
                $_SESSION['service_step'] = "waiting_for_intake";
                $response = "Please enter your intake (e.g., 'BAPA JAN23', 'BBIT SEP 23') to get a simplified timetable:<br><br><b>Type 0 to return to home.</b>";
            
        } else {
            $response = "
    <div class='bot-message'>
        <p><b>I didnt quite understand that. Kindly select any option below</b></p>
        <p></p>
        <ul class='menu-list'>
            <li>1️⃣ <b>Find venue</b> for your unit</li>
            <li>2️⃣ <b>Find the time</b> your unit will be held</li>
            <li>3️⃣ <b>Find occupied rooms</b></li>
            <li>4️⃣ <b>Find unoccupied/free rooms</b></li>
            <li>5️⃣ <b>Know which lecturer</b> is teaching a particular unit</li>
            <li>6️⃣ <b>Get a simplified timetable</b> for your intake</li>
        </ul>
        <p><b>🔄 Type 0 to return to this menu at any time.</b></p>
    </div>";
        }
    }
  
    elseif ($_SESSION['service_step'] === "waiting_for_unit") {
        if ($userMessage == "0") {
            $_SESSION['service_step'] = null;
            echo json_encode(["response" => "Returning to the main menu..."]);
            exit();
        }
        $unit = $conn->real_escape_string($userMessage);
        $query = "SELECT Venue FROM timetable_tbl WHERE UniT = '$unit' OR Unit_code = '$unit' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = "The venue for your unit is: <b>" . $row['Venue'] . "</b>";
        } else {
            $response = "Sorry, no venue found for the given unit.";
        }
        $_SESSION['service_step'] = null;
    }
    
    elseif ($_SESSION['service_step'] === "waiting_for_unit_time") {
        if ($userMessage == "0") {
            $_SESSION['service_step'] = null;
            echo json_encode(["response" => "Returning to the main menu..."]);
            exit();
        }
        $unit = $conn->real_escape_string($userMessage);
        $query = "SELECT Time FROM timetable_tbl WHERE UniT = '$unit' OR Unit_code = '$unit' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = "The time for your unit is: <b>" . $row['Time'] . "</b>";
        } else {
            $response = "Sorry, no time found for the given unit.";
        }
        $_SESSION['service_step'] = null;
    }
    
  
elseif ($_SESSION['service_step'] === "waiting_for_day") {
    if ($userMessage == "0") {
        $_SESSION['service_step'] = null;
        echo json_encode(["response" => "Returning to the main menu..."]);
        exit();
    }
    $day = ucfirst($conn->real_escape_string($userMessage));
    $query = "SELECT DISTINCT Venue, Time FROM timetable_tbl WHERE Day = '$day' ORDER BY Time";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $occupied_rooms = [];
        while ($row = $result->fetch_assoc()) {
            $occupied_rooms[] = $row['Venue'] . " at " . $row['Time']; 
        }

        
        $response = "<div style='font-size: 16px; color: #333;'>";
        $response .= "<b>Occupied rooms on <span style='color: #007BFF;'>$day</span>:</b><br>";
        $response .= "<ul style='list-style-type: none; padding-left: 0;'>";

        foreach ($occupied_rooms as $room) {
            $response .= "<li>🔒 <b>" . $room . "</b></li>";
        }

        $response .= "</ul></div>";
    } else {
        $response = "<div style='font-size: 16px; color: #333;'>";
        $response .= "<b>No occupied rooms found for <span style='color: #007BFF;'>$day</span>.</b>";
        $response .= "</div>";
    }
    $_SESSION['service_step'] = null;
}

elseif ($_SESSION['service_step'] === "waiting_for_unoccupied_rooms") {
    if ($userMessage == "0") {
        $_SESSION['service_step'] = null;
        echo json_encode(["response" => "Returning to the main menu..."]);
        exit();
    }

    $valid_days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
    $timeSlots = [];
    $time_query = "SELECT DISTINCT Time FROM timetable_tbl ORDER BY Time";
    $time_result = $conn->query($time_query);
    while ($row = $time_result->fetch_assoc()) {
        $timeSlots[] = $row['Time'];
    }

    $days = array_map('trim', explode(",", strtolower($userMessage)));
    $invalid_days = [];

    foreach ($days as $day) {
        if (!in_array($day, $valid_days)) {
            $invalid_days[] = $day;
        }
    }

    if (!empty($invalid_days)) {
        $response = "Invalid day(s) entered: <span style='color: red; font-weight: bold;'>" . implode(", ", $invalid_days) . "</span>. Please enter valid days (e.g., Monday, Tuesday, etc.).";
    } else {
        
        $all_rooms = [];
        $query = "SELECT DISTINCT Venue FROM timetable_tbl";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $all_rooms[] = $row['Venue'];
        }

        $response = "";

        foreach ($days as $day) {
            $dayUc = ucfirst($day);
            $unoccupiedWholeDay = [];
            $partiallyUnoccupied = [];

            
            $occupied_query = "SELECT Venue, Time FROM timetable_tbl WHERE Day = '$dayUc'";
            $occupied_result = $conn->query($occupied_query);

            $venueTimeMap = [];
            while ($row = $occupied_result->fetch_assoc()) {
                $venue = $row['Venue'];
                $time = $row['Time'];

                if (!isset($venueTimeMap[$venue])) {
                    $venueTimeMap[$venue] = [];
                }
                $venueTimeMap[$venue][] = $time;
            }

            foreach ($all_rooms as $venue) {
                if (!isset($venueTimeMap[$venue])) {
                    $unoccupiedWholeDay[] = $venue;
                } else {
                    $freeSlots = array_diff($timeSlots, $venueTimeMap[$venue]);
                    if (!empty($freeSlots) && count($freeSlots) < count($timeSlots)) {
                        $partiallyUnoccupied[$venue] = $freeSlots;
                    }
                }
            }

          
            $response .= "<b style='color: red;'>Unoccupied Venues on $dayUc:</b><br><br>";

            if (!empty($unoccupiedWholeDay)) {
                $response .= "🟢 <u>Unoccupied the whole day:</u><br>";
                foreach ($unoccupiedWholeDay as $v) {
                    $response .= "- $v<br>";
                }
            } else {
                $response .= "🟢 <u>Unoccupied the whole day:</u> None<br>";
            }

            if (!empty($partiallyUnoccupied)) {
                $response .= "<br>🟡 <u>Partially Unoccupied:</u><br>";
                foreach ($partiallyUnoccupied as $venue => $slots) {
                    $response .= "- $venue: Free during <b>" . implode(", ", $slots) . "</b><br>";
                }
            } else {
                $response .= "<br>🟡 <u>Partially Unoccupied:</u> None<br>";
            }

            $response .= "<br>------------------------------------<br><br>";
        }
    }

    $_SESSION['service_step'] = null;
}

    // lecturer
    elseif ($_SESSION['service_step'] === "waiting_for_lecturer") {
        if ($userMessage == "0") {
            $_SESSION['service_step'] = null;
            echo json_encode(["response" => "Returning to the main menu..."]);
            exit();
        }
        $unit = $conn->real_escape_string($userMessage);
        $query = "SELECT Lecturer_Name FROM timetable_tbl WHERE UniT = '$unit' OR Unit_code = '$unit' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = "The lecturer for your unit is: <b>" . $row['Lecturer_Name'] . "</b>";
        } else {
            $response = "Sorry, no lecturer found for the given unit.";
        }
        $_SESSION['service_step'] = null;
    }

    // Handling intakes
    
    elseif ($_SESSION['service_step'] === "waiting_for_intake") {
        if ($userMessage == "0") {
            $_SESSION['service_step'] = null;
            echo json_encode(["response" => "Returning to the main menu..."]);
            exit();
        }
    
        $intake = $conn->real_escape_string($userMessage);
        $query = "SELECT DISTINCT Day, Time, Unit, Venue, Lecturer_Name FROM timetable_tbl 
                  WHERE Intake = '$intake' 
                  ORDER BY FIELD(Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), Time";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            $filename = "timetable_" . str_replace(' ', '_', strtolower($intake)) . ".xlsx";
            $directory = "generated_excels/";
    
            
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
    
            $filePath = $directory . $filename;
    
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Timetable");
    
           
            $sheet->setCellValue("A1", "Day");
            $sheet->setCellValue("B1", "Time");
            $sheet->setCellValue("C1", "Unit");
            $sheet->setCellValue("D1", "Venue");
            $sheet->setCellValue("E1", "Lecturer_Name"); 
    
            
            $row = 2;
            while ($data = $result->fetch_assoc()) {
                $sheet->setCellValue("A$row", $data['Day']);
                $sheet->setCellValue("B$row", $data['Time']);
                $sheet->setCellValue("C$row", $data['Unit']);
                $sheet->setCellValue("D$row", $data['Venue']);
                $sheet->setCellValue("E$row", $data['Lecturer_Name']); 
                $row++;
            }
    
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
    
            $response = "Your timetable for <b>$intake</b> has been generated. Click below to download:<br><br>";
            $response .= "<a href='$filePath' download><b>📥 Download Timetable (Excel)</b></a>";
        } else {
            $response = "Sorry, no timetable found for <b>$intake</b>.";
        }
    
        $_SESSION['service_step'] = null;
        echo json_encode(["response" => $response]); 
        exit();
    }
    
    echo json_encode(["response" => $response]);
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Chatbot</title>
    <link rel="stylesheet" href="styling.css">
    <style>
        .chatbot-icon { position: fixed; bottom: 20px; right: 20px; background: #6c5ce7; padding: 15px; border-radius: 50%; cursor: pointer; animation: bounce 1.5s infinite; }
        body { font-family: Arial, sans-serif; text-align: center; }
        #chatbox { width: 250%; height: 400px; border: 1px solid #ccc; overflow-y: auto; padding: 10px; margin: auto; }
        #userInput { width: 250%; padding: 20px; }
        button {width: 250%; padding: 20px; margin: 5px; cursor: pointer; }
        .bot{
            position: relative;
            left: 50px;
            top: 0px;
        }
        
        .sidebar a.active, 
.footer-columns ul li a.active, 
.dashboard-cards a.active, 
.profile-dropdown a.active {
    background-color: #6c5ce7;
    color: white;
    border-radius: 5px;
}
.bot {
    width: 60%;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    top: 60px;
}

.uni {
    color: #333;
    margin-bottom: 10px;
}

#chatbox {
    width: 100%;
    height: 400px;
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow-y: auto;
    padding: 15px;
    background-color: #fff;
    text-align: left;
}

#userInput {
    width: 90%;
    padding: 12px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

button {
    width: 95%;
    padding: 12px;
    margin: 10px 0;
    border: none;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

.bot-message {
    background-color: #e9e9e9;
    padding: 10px;
    border-radius: 5px;
    margin: 5px 0;
}

.user-message {
    background-color: #007bff;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin: 5px 0;
    text-align: right;
}

/* Responsive Design */
@media (max-width: 768px) {
    .bot {
        width: 90%;
    }
    #userInput {
        width: 85%;
    }
    button {
        width: 90%;
    }
}
body {
        background: linear-gradient(135deg, #e0eafc, #cfdef3);
        min-height: 120vh;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
    }
        .upload-header {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        padding: 15px 0;
        width: calc(100% - 250px);
        position: fixed;
        left: 250px;
        top: 0;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 600;
        color: #333;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 100;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px);}
        to { opacity: 1; transform: translateY(0);}
    }

    </style>
</head>
<body>
<div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>
<?php include 'stdsidebar.php'; ?>   
<div class="upload-header">INTERACT WITH YOUR CHATBOT</div>
<div class="bot">
<div class="uni"><h2>University Chatbot</h2></div>
    <div id="chatbox"></div>

    <input type="text" id="userInput" placeholder="start by saying hello" onkeypress="handleKeyPress(event)" />
    <button onclick="sendMessage()">Send</button>

    <script>
        function sendMessage() {
            var userInput = document.getElementById("userInput").value;
            if (userInput.trim() === "") return;

            var chatbox = document.getElementById("chatbox");
            chatbox.innerHTML += "<p><b>You:</b> " + userInput + "</p>";

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "bot.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var responseJson = JSON.parse(xhr.responseText);
                    chatbox.innerHTML += "<p><b>Bot:</b> " + responseJson.response + "</p>";
                    chatbox.scrollTop = chatbox.scrollHeight;
                }
            };

            xhr.send("message=" + encodeURIComponent(userInput));
            document.getElementById("userInput").value = "";
        }

        function handleKeyPress(event) {
            if (event.key === "Enter") {
                sendMessage();
            }
        }
    </script>
</div>
<div class="chatbot-icon" onclick="openChatbot()">
    💬
</div>
</body>
</html