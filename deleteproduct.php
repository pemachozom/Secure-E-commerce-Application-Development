<?php
session_start();

// Connect to the database (modify these credentials)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'user'; // Change this to your actual database name

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if the user is logged in as a seller
    if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'Seller') {
        // Redirect to the login page for unauthorized users
        header("Location: login.php");
        exit();
    }

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get product name from the form
        $productName = $_POST['productName'];

        // Prepare and execute the SQL query to delete the product
        $sql = "DELETE FROM products WHERE productName = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('s', $productName);
            $stmt->execute();

            // Check if the deletion was successful
            if ($stmt->affected_rows > 0) {
                // Sanitize the echoed message
                $successMessage = htmlspecialchars("Successfully deleted!");
                echo "<script>alert('$successMessage')</script>";
                echo "<script>window.location.href = 'sellerhome.html'</script>";
            } else {
                echo "Error: Product not found or could not be deleted.";
            }

            $stmt->close();
        } else {
            throw new Exception("Error: Database error.");
        }
    } else {
        throw new Exception("Error: Invalid request.");
    }
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

