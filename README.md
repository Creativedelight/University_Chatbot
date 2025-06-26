
# 🎓 MKU Timetable Management Chatbot

This is the chatbot module from the **Mount Kenya University Timetable Management System**, developed using PHP and MySQL. It allows students and staff to retrieve timetable information using natural language queries — enhancing access to schedules, room bookings, and unit allocations.

---

## 🧠 Features

- 🧾 Fetch **timetables by course, year, or day**
- 🧑‍🏫 Check **lecturer-specific schedules**
- 🏛️ Look up **room or lab availability**
- 📘 Respond to **frequently asked questions**
- 🧠 Basic **keyword/intent matching** logic (no ML required)
- 🧩 Easy to integrate into HTML pages or forms

---

## 🛠️ Technologies Used

- **PHP 7+** (pure PHP, no framework)
- **MySQL** (for timetable data)
- **HTML/CSS** (for chat interface)
- **JavaScript** — for AJAX chat interaction

---

## 📂 Project Structure

```
mku-chatbot/
├── index.html                # Frontend interface
├── chatbot.php               # Main response logic
├── db.php                    # MySQL connection
├── assets/
│   └── style.css             # Optional styling
└── README.md
```

---

## 🗃️ Database Schema (Simplified)

```sql
CREATE TABLE timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Day VARCHAR(100),
    Time VARCHAR(50),
    Unit_name VARCHAR(100),
    unit_code VARCHAR(20),
    Lecturer_Name VARCHAR(100)
);
```

---

## 🚀 How to Run

### 1. Clone or download the project
```bash
git clone https://github.com/University_Chatbot/mku-chatbot.git
```

### 2. Import the database
- Open **phpMyAdmin**
- Create a new database (e.g. `timetable`)
- Import the `timetable.sql` file (not included here — you can export from your local DB)

### 3. Configure the database connection
Edit `database.php`:
```php
$host = "localhost";
$user = "root";
$password = "";
$database = "timetable";
$conn = new mysqli($host, $user, $password, $database);
```

### 4. Start a local server
Using XAMPP, WAMP, or PHP built-in:
```bash
php -S localhost:8000
```

Visit: [http://localhost:8000/Chatot/bot.php](http://localhost:8000/Chatbot/bot.php)

---

## 💬 Sample Chat Queries

- "What is the timetable for Computer Science Year 3?"
- "When is BIT 211 scheduled?"
- "Which units does Mr. Mwangi teach?"
- "Is Lab 2 free on Thursday?"
- "Where is the Software Engineering class held?"

---

## 🧠 Chatbot Logic

- Input is parsed for keywords like course, year, lecturer, room, day
- Logic checks against the timetable table using SQL `LIKE` or exact match
- Response is returned in plain tex
- Fallback messages are used when no match is found

---

## ✨ Future Improvements

- Include **speech-to-text** for voice queries

---

## 👩‍💻 Author

**Faith Munuhe**  
Final Year Student — Mount Kenya University  
GitHub: [https://github.com/Creativedelight](https://github.com/Creativedelight)  
Email: [munuhefay@gmail.com]

---


