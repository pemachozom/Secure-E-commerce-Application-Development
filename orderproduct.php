
<?php
session_start();

// Check if the user is logged in and is a buyer
if ( $_SESSION['user_role'] !== 'Buyer') {
    // Redirect to the login page for unauthorized users
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Home</title>
    <link rel="stylesheet" href="./css/sellerhome.css">
</head>
<style>
    form {
        display: grid;
        gap: 10px;
        max-width: 400px;
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;  
    }

    span {
        font-weight: bold;
    }

    .orderform {
        border: 1px solid black;
        width: 30%;
        padding: 35px;
        position: relative;
        left: 35%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        background-color: rgb(204, 218, 231);
    }

    form input {
        height: 0.8cm;
        padding: 5px;
    }

    .error-message {
        color: red;
        margin-top: 10px;
    }
</style>
<body>
    
    <div class="banner">
        <div class="navbar">
            <!-- <img class="logo" src="logo.png"> -->
            <p class="logo" style="color: white; font-size: 25px;">E-Deals</p>
            <ul>
                <li><a href="buyerhome.html">Home</a></li>
                <li><a href="viewproduct.html">View Product</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div><br><br><br>

        <div class="orderform">
            <div class="order_details">
                <p><strong>Product Details</strong></p><br>
                <p><span id="productName">Product Name: <?php echo isset($_POST['productName']) ? $_POST['productName'] : ''; ?></span></p>
                <p><span id="productDescription">Product Description: <?php echo isset($_POST['productDescription']) ? $_POST['productDescription'] : ''; ?></span></p>
                <p><span id="productPrice">Product Price: <?php echo isset($_POST['productPrice']) ? $_POST['productPrice'] : ''; ?></span></p>
                <p><span id="totalQuantity">Total Quantity: <?php echo isset($_POST['productQuantity']) ? $_POST['productQuantity'] : ''; ?></span></p>
            </div><br><br>

            <form id="orderForm">
                <label for="customerName">Your Name:</label>
                <input type="text" id="customerName" name="customerName" required>

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" oninput="validateQuantity()" required>
                <div id="errorMessage" class="error-message"></div>


                <label for="shippingAddress">Shipping Address:</label>
                <input type="text" id="shippingAddress" name="shippingAddress" required>

                <button type="button" onclick="submitForm()">Submit</button>
            </form>

        </div>
    </div>

    <script>
        function validateQuantity() {
            var quantityInput = document.getElementById('quantity').value;
            var totalQuantity = <?php echo isset($_POST['productQuantity']) ? $_POST['productQuantity'] : 0; ?>;
            var errorMessage = document.getElementById('errorMessage');

            if (quantityInput > totalQuantity || quantityInput <= 0) {
                errorMessage.innerText = 'Error: Product quantity unavailable.';
            } else {
                errorMessage.innerText = '';
                return true;
            }
        }

        function submitForm() {
            if(validateQuantity()){
                alert("Successfully ordered")
                window.location.href = "buyerhome.html"
            } // Perform validation before submission

        }
    </script>
</body>
</html>
