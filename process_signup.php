<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Connect to the database (modify these credentials)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'user';

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security
        $phone = $_POST['phone'];
        $role = isset($_POST['role']) ? $_POST['role'] : 'Buyer'; // Default role is Buyer

        $_SESSION['email'] = $email;

        // An OTP is generated using the generateOTP function, and it is stored in the database.
        function generateOTP($length = 6) {
            $otp = '';
            for ($i = 0; $i < $length; $i++) {
                $otp .= rand(0, 9); // Generate a random digit between 0 and 9
            }
            return $otp;
        }

        $OTP = generateOTP();

        // The generated OTP is then included in the database insert statement.
        // Insert data into the database using prepared statement
        $sql = "INSERT INTO users (username, email, password, otp, phone, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Error handling for the prepared statement
        if (!$stmt) {
            throw new Exception("Error preparing SQL statement: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $username, $email, $password, $OTP, $phone, $role);

        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            // Server settings for Gmail SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '12210024.gcit@rub.edu.bt'; // Your Gmail address
            $mail->Password = 'jemyadslmjyqtyqg'; // Your Gmail password
            $mail->SMTPSecure = 'ssl'; // Use TLS encryption
            $mail->Port = 465; // TLS port (587)

             // Recipient and message settings
            $mail->setFrom('12210024.gcit@rub.edu.bt');
            $mail->addAddress($email); // Replace with the recipient's email address
            $mail->Subject = 'Registration Successful! Use the OTP before 5 minutes';
            // $mail->Body = 'Your OTP: ' . $OTP;
            $mail->Body = 'Your OTP: ' . htmlspecialchars($OTP, ENT_QUOTES, 'UTF-8');


            // Send the email
            if ($mail->send()) {
                header("Location: otp.html");
            } else {
                throw new Exception("Email not sent!");
            }
        } else {
            throw new Exception("Error executing SQL statement: " . $stmt->error);
        }

        $stmt->close();
    }
} catch (Exception $e) {
    echo '<script>alert("Caught exception: ' . $e->getMessage() . '");</script>';
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
