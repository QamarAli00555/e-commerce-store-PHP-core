<?php
// product_details.php

session_start();
include('../Config/dbConfig.php');
// Check if product ID is provided via GET
if (isset($_GET['id'])) {
    $productId = $_GET['id'];


    $sql = "SELECT *
    FROM Products 
    JOIN Subcategories ON Products.subcategory_id = Subcategories.subcategory_id 
    JOIN Categories ON Subcategories.parent_category_id = Categories.category_id
    WHERE Products.product_id=$productId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
    }
} else {
    echo "Product ID is missing.";
}

// Assuming you have already established a database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get product ID, quantity, and user ID from the form
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    if (!isset($_SESSION['userId'])) {
        echo "<script>alert('User Id not found.'); window.location='index.php';</script>";
        exit();
    }
    $userId = $_SESSION['userId'];
    //  die("CALL UpdateCartAndCartItems($productId, $quantity, $userId)");
    // Prepare and bind parameters
    $stmt = $conn->prepare("CALL UpdateCartAndCartItems(?, ?, ?)");
    $stmt->bind_param("iii", $productId, $quantity, $userId);
    // Execute the stored procedure
    $stmt->execute();

    // Check if the execution was successful
    if ($stmt->affected_rows > 0) {
        $status = "Stored procedure executed successfully.";
    } else {
        $status = "Error executing stored procedure: " . $stmt->error;
    }
    $stmt->execute();
    die("Product added to cart successfully.");

    // Check if the product already exists in the cart for the user
    // $existingCartQuery = "SELECT * FROM cart JOIN cartitems on cart.Id = cartitems.cartItemsId WHERE cart.UserId = '$userId' AND cartitems.productId = '$productId'";
    // $existingCartResult = $conn->query($existingCartQuery);

    // if ($existingCartResult->num_rows > 0) {
    //     // Product already exists in the cart for the user, update the quantity
    //     $existingCartItem = $existingCartResult->fetch_assoc();
    //     $newQuantity =  $quantity;
    //     // Update the quantity in the cart table
    //     $updateQuery = "UPDATE cart SET items = '$newQuantity' WHERE productId = '$productId' AND UserId = '$userId'";
    //     if ($conn->query($updateQuery) === TRUE) {
    //         die("Quantity updated successfully.");
    //     } else {
    //         die("Error updating quantity: " . $conn->error);
    //     }
    // } else {
    //     // Product does not exist in the cart for the user, insert a new record
    //     $insertQuery = "INSERT INTO cart (productId, items, UserId) VALUES ('$productId', '$quantity', '$userId')";
    //     if ($conn->query($insertQuery) === TRUE) {
    //         die("Product added to cart successfully.");
    //     } else {
    //         die("Error adding product to cart: " . $conn->error);
    //     }
    // }
}



?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!DOCTYPE html>
<html lang="en">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>eCommerce Product Detail</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <link rel="stylesheet" href="../css//product_details.css">
</head>

<body>

    <div class="container">
        <div class="card" style="width: 100%;height: 100%;">
            <div class="table-title" style="margin: 21px;font-size: 25px;font-weight: bold;align-items: center;text-align: center;border-bottom: 1px solid black;">Product Details</div>
            <div class="container-fliud">
                <div class="preview col-md-6">

                    <div class="preview-pic tab-content">
                        <div class="tab-pane active" id="pic-1">
                            <img src="<?php echo   $row['image']  ?>" />
                        </div>
                    </div>

                </div>
                <div class="details col-md-6">
                    <h3 class="product-title"><?php echo $row['product_name'] ?></h3>
                    <p class="product-description"><?php echo $row['description'] ?></p>
                    <h4 class="price">Current Price: <span>$<?php echo $row['price'] ?></span></h4>
                    <h5 class="">Size: <span style="color: black"><?php echo $row['size'] ?></span></h5>
                    <h5 class="">Color: <span style="color: black"><?php echo $row['colors'] ?></span></h5>
                    <h5 class="price">Quantity: <span id="quantity" style="color: black; margin:5px">1</span></h5>
                    <h5 class="price">
                        <span id="quantity" style="color: black">
                            <button class="increment btn btn-default" type="button">+</button>
                            <button class="decrement btn btn-default" type="button">-</button>
                        </span>
                    </h5>
                    <div class="action" style="margin-top: 50px;">


                    </div>
                    <div class="action" style="margin-top: 50px;">
                        <button class="add-to-cart btn btn-default" type="button" onclick="addToCart(<?php echo $row['product_id']; ?>, document.getElementById('quantity').innerHTML)">Add to Cart</button>
                    </div>

                </div>

            </div>
        </div>
    </div>
    </div>

    <script src="../js/product_details.js"></script>
</body>

</html>