<?php
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'user';

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Check the connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        if (isset($_SESSION['email'])) {
            $userEmail = $_SESSION['email'];

            $query = $conn->prepare("SELECT otp, timestamp_column FROM users WHERE email = ?");
            $query->bind_param("s", $userEmail);
            $query->execute();
            $result = $query->get_result();

            // Error handling for query execution
            if (!$result) {
                throw new Exception("Error getting result: " . $conn->error);
            }

            $user = $result->fetch_assoc();

            if ($user) {
                $storedOTP = $user['otp'];
                $userEnteredOTP = $_POST['otp'];
                $otpTimestamp = $user['timestamp_column'];

                $expirationTime = 5*60; // 5 minutes in seconds

                if ($userEnteredOTP == $storedOTP) {
                    if (time() - strtotime($otpTimestamp) <= $expirationTime) {
                        // OTPs match; update the status to "verified"
                        $updateQuery = $conn->prepare("UPDATE users SET status = 'Verified' WHERE email = ?");
                        $updateQuery->bind_param("s", $userEmail);

                        // Error handling for update query
                        if (!$updateQuery->execute()) {
                            throw new Exception("Error updating status: " . $updateQuery->error);
                        }

                        $updateQuery->close();
                        header("Location: login.html");
                    } else {
                        throw new Exception("OTP expired!");
                    }
                } else {
                    throw new Exception("Invalid OTP!");
                }
            } else {
                throw new Exception("Email Not found!");
            }
        } else {
            throw new Exception('Session email not found. Unauthorized access.');
        }

        $query->close();
        $conn->close();
    }
} catch (Exception $e) {
    echo '<script>alert("Caught exception: ' . $e->getMessage() . '");</script>';
    echo '<script>window.location.href = "index.html";</script>';
}
?>
