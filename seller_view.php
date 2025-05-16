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
        header("Location: login.html    ");
        exit();
    }

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }


    // Fetch products from the database
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    // Display products
    if ($result !== false && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Display product details with output sanitization
            echo "<div class='items'>";
            echo "Product Name: " . htmlspecialchars($row["productName"]) . "<br>";
            echo "Product Description: " . htmlspecialchars($row["productDescription"]) . "<br>";
            echo "Product Price: Nu. " . htmlspecialchars($row["productPrice"]) . "<br>";
            echo "Product Quantity: " . htmlspecialchars($row["productQuantity"]) . "<br>";
            echo "Product Image: <img id='prodimg' src='" . htmlspecialchars($row["productImage"]) . "' alt='Product Image' style='max-width: 200px;'><br>";

            // Add Delete button with a link to the delete script
            echo "<form action='deleteproduct.php' method='post'>
                    <input type='hidden' name='productName' value='" . htmlspecialchars($row['productName']) . "'>";

            // Check if the logged-in user is the owner of the product
             if ($_SESSION['user_role'] === 'Seller') {
                // Display the order button
                echo "<button type='submit'>Delete</button>";
            }

            echo "</form>";
            echo "</div>";
        }
    } else {
        throw new Exception("No products found.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
