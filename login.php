<?php
session_start();

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'user';
error_reporting(E_ALL);
ini_set("display_errors", 0);

// function customErrorHandler($errno, $errstr, $errfile, $errline) {
//     $timestamp = date('Y-m-d H:i:s');
//     $message = "Error: [$errno] $errstr - $errfile: $errline";
//     $logData = "$timestamp, $errno, $errstr, $errfile, $errline" . PHP_EOL;
//     error_log($logData, 3, "error_log.csv");
// }

// set_error_handler("customErrorHandler");


try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        throw new Error("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        $enteredRole = htmlspecialchars($_POST["role"]);

        // Use prepared statement for the SQL query
        $sql = "SELECT email, password, status, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error preparing SQL statement.");
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($dbEmail, $dbPassword, $status, $dbRole);
            $stmt->fetch();

            // Verify the password using password_verify
            if (password_verify($password, $dbPassword)) {
                if ($status == "Verified") {
                    if ($enteredRole == $dbRole) {
                        $_SESSION["user_email"] = $dbEmail;
                        $_SESSION["user_role"] = $dbRole;

                        if ($enteredRole == "Seller") {
                            echo '<script>alert("Login Successful for seller!");</script>';
                            header("Location: sellerhome.html");
                        } elseif ($enteredRole == "Buyer") {
                            echo '<script>alert("Login Successful for buyer!");</script>';
                            header("Location: buyerhome.html");
                        } else {
                            
                            throw new Exception("Invalid user role.");
                            // $errorMessage = "Invalid ROle $enteredRole";
                            // customErrorHandler(E_USER_NOTICE, $errorMessage, _FILE, __LINE_);
                        }
                    } else {
                        throw new Exception("Incorrect Role");
                        // $errorMessage = "Invalid ROle $enteredRole";
                        // customErrorHandler(E_USER_NOTICE, $errorMessage, _FILE, __LINE_);
                    }
                } else {
                    throw new Exception("Email not verified");
                }
            } else {
                throw new Exception("Incorrect email or password");
            }
        } else {
            throw new Exception("Incorrect email or password");
        }

        $stmt->close();
    } else {
        throw new Exception("Invalid request method");
    }
    // $logData = [
    //     'level' => 'error',
    //     'message' => 'An error occurred in login.php: ' . $e->getMessage(),
    // ];

    // $nodeJsUrl = 'http://localhost:8080'; // Update with your Node.js server URL
    // $ch = curl_init($nodeJsUrl);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($logData));
    // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    // curl_exec($ch);
    // curl_close($ch);

} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

?>
