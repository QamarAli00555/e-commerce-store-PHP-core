<?php
// product_details.php

session_start();
include('../Config/dbConfig.php');

$grandTotal = 0.0;
// Check if product ID is provided via GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // $userId = $_GET['id'];
    if (!isset($_SESSION['userId'])) {
        echo "<script>alert('User Id not found.'); window.location='index.php';</script>";
        exit();
    }
    $sql = "SELECT Products.product_id, Products.product_name, Categories.category_name,Products.price,
    Products.description, Products.image, cartItems.quantity
    FROM Products 
    JOIN Subcategories ON Products.subcategory_id = Subcategories.subcategory_id 
    JOIN Categories ON Subcategories.parent_category_id = Categories.category_id 
    JOIN cartItems on cartitems.productId = 			products.product_id
    JOIN cart ON cart.Id = cartitems.cartItemsId
    WHERE cart.UserId={$_SESSION['userId']} AND cart.isShipped=0
    ORDER BY products.product_id
    ";
    $result = $conn->query($sql);

    // if ($result->num_rows > 0) {
    //     $row = $result->fetch_assoc();
    // } else {
    //     echo "Product not found.";
    // }
}

// Check if product ID is provided and valid
// if (isset($_POST['productId']) && is_numeric($_POST['productId'])) {
//     $productId = $_POST['productId'];
//     // Prepare and execute DELETE query
//     $stmt = $conn->prepare("DELETE FROM cart WHERE productId = ?");
//     $stmt->bind_param("i", $productId);
//     $stmt->execute();

//     // Check if the deletion was successful
//     if ($stmt->affected_rows > 0) {
//         // Item removed successfully
//         die("Item removed from cart.");
//     } else {
//         // Item not found in cart or deletion failed
//         die("Failed to remove item from cart.");
//     }
// }

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['method']) && $_POST['method'] == 'updateCart') {
        // Get product ID and quantity from the form
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $userId = $_SESSION['userId'];
        // die($productId . ' - ' . $quantity . ' - ' . $userId);
        // Insert product ID, timestamp, and quantity into the cart table
        // Prepare and bind parameters
        $stmt = $conn->prepare("CALL UpdateCartAndCartItems(?, ?, ?)");
        $stmt->bind_param("iii", $productId, $quantity, $userId);

        if ($stmt->execute()) {
            die(true);
        } else {
            die("Error: " . $sql . "<br>" . $conn->error);
        }
    } else if (isset($_POST['method']) && $_POST['method'] == 'deleteCart') {
        // Get product ID and quantity from the form
        $productId = $_POST['product_id'];
        $userId = $_SESSION['userId'];
        //die($productId . ' - ' . $userId);
        // Insert product ID, timestamp, and quantity into the cart table
        $stmt = $conn->prepare("CALL DeleteProductFromCart(?, ?)");
        $stmt->bind_param("ii", $userId, $productId);

        if ($stmt->execute()) {
            die(true);
        } else {
            die("Error: " . $sql . "<br>" . $conn->error);
        }
    } else if (isset($_POST['method']) && $_POST['method'] == 'checkouts') {
        // Get product ID and quantity from the form
        $quantity = $_POST['price'];
        $userId = $_SESSION['userId'];
        //die($quantity . ' - ' . $userId);
        // Insert product ID, timestamp, and quantity into the cart table
        $stmt = $conn->prepare("CALL InsertCheckout(?, ?)");
        $stmt->bind_param("ii", $userId, $quantity);

        if ($stmt->execute()) {
            die(true);
        } else {
            die("Error: " . $sql . "<br>" . $conn->error);
        }
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<!--divinectorweb.com-->

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../css/style.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <div class="table-title" style="margin: 21px;font-size: 25px;font-weight: bold;align-items: center;text-align: center;border-bottom: 1px solid black;">Shopping Cart</div>
        <div class="project">
            <div class="shop">

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Calculate total price
                        $totalPrice = $row['price'] * $row['quantity'];
                        $grandTotal = $grandTotal + $totalPrice;
                ?>
                        <div class="box">
                            <img style="max-width: 400px;" src="<?php echo $row['image'] ?>">
                            <div class="content">
                                <h3 style="display:none" class="prod_id"><?php echo $row['product_id'] ?></h3>
                                <h3 style="margin-top:-5px"><?php echo $row['product_name'] ?></h3>
                                <p class="unit"><?php echo $row['description'] ?></p>
                                <h4>Price: $<?php echo $row['price'] ?></h4>
                                <p class="unit">Quantity:
                                <h5 class="price">
                                    <span class="quantity-control" data-product-id="<?php echo $row['product_id']; ?>" data-items="<?php echo $row['quantity']; ?>">
                                        <button class="increment btn btn-default" type="button" data-product-id="<?php echo $row['product_id']; ?>">+</button>
                                        <span class="quantity" id="qty" style="margin:5px"><?php echo $row['quantity'] ?></span>
                                        <button class="decrement btn btn-default" type="button" data-product-id="<?php echo $row['product_id']; ?>">-</button>
                                    </span>
                                </h5>
                                </p>
                                <!-- Echo the total price here -->
                                <h4 class="total-price">Total Price: $<?php echo $totalPrice ?></h4>
                                <p class="btn-area btn2 remove-product" data-product-id="<?php echo $row['product_id']; ?>"><i aria-hidden="true" class="fa fa-trash"></i> <span>Remove</span></p>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>No products found</td></tr>";
                }
                ?>



            </div>
            <div class="right-bar">
                <hr>
                <p><span>Total</span> <span id="grandTotal">$<?php echo $grandTotal ?></span></p>
                <hr>
                <a class=" checkouts-product"><i class="fa fa-shopping-cart"></i>Checkout</a>



                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script>
                    $(document).ready(function() {


                        $(".quantity-control .increment").click(function() {
                            var productId = $(this).data('product-id');
                            var quantityElement = $(this).siblings(".quantity");
                            var quantity = parseInt(quantityElement.text());
                            quantityElement.text(quantity + 1);
                            updateTotalPrice($(this), productId);
                        });

                        $(".quantity-control .decrement").click(function() {
                            var productId = $(this).data('product-id');
                            var quantityElement = $(this).siblings(".quantity");
                            var quantity = parseInt(quantityElement.text());
                            if (quantity > 1) {
                                quantityElement.text(quantity - 1);
                                updateTotalPrice($(this), productId);
                            }
                        });

                        function updateTotalPrice(button, productId) {
                            var quantityElement = button.siblings(".quantity");
                            var quantity = parseInt(quantityElement.text());
                            var price = parseFloat(button.closest('.content').find('h4').text().replace('Price: $', ''));
                            var totalPriceElement = button.closest('.content').find('.total-price');
                            var totalPrice = quantity * price;
                            totalPriceElement.text('Total Price: $' + totalPrice.toFixed(2));

                            $.ajax({
                                type: "POST",
                                url: "",
                                data: {
                                    method: "updateCart",
                                    product_id: productId,
                                    quantity: quantity,
                                },
                                success: function(response) {
                                    if (response == true) {
                                        window.location.reload();
                                    } else {
                                        alert(response);
                                    }

                                },

                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error("AJAX Error:", textStatus, errorThrown);
                                    var error = new Error(errorThrown);
                                },
                            });
                        }

                        $(".remove-product").click(function() {
                            var productId = $(this).data('product-id');
                            $.ajax({
                                type: "POST",
                                url: "",
                                data: {
                                    method: "deleteCart",
                                    product_id: productId,
                                },
                                success: function(response) {
                                    if (response == true) {
                                        window.location.reload();
                                    } else {
                                        alert(response);
                                    }

                                },

                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error("AJAX Error:", textStatus, errorThrown);
                                    var error = new Error(errorThrown);
                                },
                            });
                        });

                        $(".checkouts-product").click(function() {
                            var totalPrice = <?php echo $grandTotal ?>;
                            if (totalPrice == 0) {
                                alert('Transaction amount is invalid');
                                return
                            }
                            $.ajax({
                                type: "POST",
                                url: "",
                                data: {
                                    method: "checkouts",
                                    price: totalPrice,
                                },
                                success: function(response) {
                                    if (response == true) {
                                        alert('Record Saved Successfully');
                                        window.location.href = 'index.php';
                                    } else {
                                        alert(response);
                                    }

                                },

                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error("AJAX Error:", textStatus, errorThrown);
                                    var error = new Error(errorThrown);
                                },
                            });

                        });

                    });
                </script>
                <script>

                </script>
            </div>
        </div>
    </div>
</body>

</html>