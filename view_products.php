<?php
session_start();

// Connect to the database (modify these credentials)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'user'; // Change this to your actual database name

 

    // Check if the user is logged in as a buyer
    try {
        if ($_SESSION['user_role'] !== 'Buyer') {
            // Redirect to the login page for unauthorized users
            header("Location: login.html");
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
        if ($result !== false) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Display product details
                    echo "<div class='items'>";
                    echo "Product Name: " . htmlspecialchars($row["productName"]) . "<br>";
                    echo "Product Description: " . htmlspecialchars($row["productDescription"]) . "<br>";
                    echo "Product Price: Nu. " . htmlspecialchars($row["productPrice"]) . "<br>";
                    echo "Product Quantity: " . htmlspecialchars($row["productQuantity"]) . "<br>";
                    echo "Product Image: <img id='prodimg' src='" . htmlspecialchars($row["productImage"]) . "' alt='Product Image' style='max-width: 200px;'><br>";
    
                    // Add Order button with a link to the order HTML page
                    echo "<form action='orderproduct.php' method='post'>
                            <input type='hidden' name='productName' value='" . htmlspecialchars($row['productName']) . "'>
                            <input type='hidden' name='productDescription' value='" . htmlspecialchars($row['productDescription']) . "'>
                            <input type='hidden' name='productPrice' value='" . htmlspecialchars($row['productPrice']) . "'>
                            <input type='hidden' name='productQuantity' value='" . htmlspecialchars($row['productQuantity']) . "'>
                            <input type='hidden' name='productImage' value='" . htmlspecialchars($row['productImage']) . "'>";
    
                    // Check if the logged-in user is not the owner of the product
                    if ($_SESSION['user_role'] === 'Buyer') {
                        // Display the order button
                        echo "<button type='submit'>Order</button>";
                    }
    
                    echo "</form>";
                    // Check if the logged-in user is the owner of the product
    
                    echo "</div>";
                }
            } else {
                throw new Exception("No products found.");
            }
        } else {
            throw new Exception("Error executing query: " . $conn->error);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
    ?>