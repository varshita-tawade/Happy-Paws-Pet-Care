<?php
// 1. Database Configuration
$servername = "localhost";
$username = "root";
$password = "";      // Usually empty for XAMPP
$dbname = "petcare"; // Your Database Name in phpMyAdmin

// 2. Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// 3. Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Capture Form Data via POST Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = $_POST['pet_name'];
    $service_type = $_POST['service_type'];
    $booking_date = $_POST['booking_date'];

    // 5. Execute SQL Query to Insert Data
    $sql = "INSERT INTO bookings (pet_name, service_type, booking_date) 
            VALUES ('$pet_name', '$service_type', '$booking_date')";

    if ($conn->query($sql) === TRUE) {
        // Send 'success' message if record created successfully
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
}

// Close Database Connection
$conn->close();
?>