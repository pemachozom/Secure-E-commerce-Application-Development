<?php

session_start();

// Connect to the database (modify these credentials)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'user'; // Change this to your actual database name

try {
    // Check if the user is logged in as a seller
    if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'Seller') {
        // Redirect to the login page for unauthorized users
        header("Location: login.html");
        exit();
    }

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get product input (sanitize and validate as needed)
        $productName = htmlspecialchars($_POST["productName"]);
        $productDescription = htmlspecialchars($_POST["productDescription"]);
        $productPrice = floatval($_POST["productPrice"]); // Convert to float for security
        $productQuantity = htmlspecialchars($_POST["productQuantity"]);

        // File upload handling
        $targetDir = "uploads/"; // Create this directory in your project
        $targetFile = $targetDir . basename($_FILES["productImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // / Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["productImage"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else { 
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["productImage"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            throw new Exception("Sorry, your file was not uploaded.");
        } else {
            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
                echo "The file " . basename($_FILES["productImage"]["name"]) . " has been uploaded.";
            } else {
                throw new Exception("Sorry, there was an error uploading your file.");
            }
        }

        // Insert data into the database using prepared statement
        $sql = "INSERT INTO products (productName, productDescription, productPrice, productQuantity, productImage) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ssdss", $productName, $productDescription, $productPrice, $productQuantity, $targetFile);
        $stmt->execute();
        $stmt->close();

        echo '<script>';
        echo 'alert("Product added successfully!");';
        echo 'window.location.href = "sellerhome.html";';  // Redirect to sellerhome.html
        echo '</script>';
    }
} catch (Exception $e) {
    echo '<script>alert("Caught exception: ' . $e->getMessage() . '");</script>';
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
