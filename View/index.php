<?php

session_start();
// session_unset();
// session_destroy();
// Database connection
include('../Config/dbConfig.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Save userId in PHP session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    $_SESSION['userId'] = $_POST['userId'];
    die("User ID Saved!");
}

// Query to retrieve products with pagination
$sql = "SELECT Products.product_id, Products.product_name, Products.price, Subcategories.subcategory_name, Categories.category_name 
        FROM Products 
        JOIN Subcategories ON Products.subcategory_id = Subcategories.subcategory_id 
        JOIN Categories ON Subcategories.parent_category_id = Categories.category_id 
        ";
$result = $conn->query($sql);

// Query to get total number of rows for pagination
$sql_count = "SELECT COUNT(*) AS total FROM Products";
$count_result = $conn->query($sql_count);
$row_count = $count_result->fetch_assoc();
// $total_pages = ceil($row_count['total'] / $results_per_page);
// Pagination
$results_per_page = 5;
if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$start_from = ($page - 1) * $results_per_page;

// Query to retrieve products with pagination
$sql = "SELECT Products.product_id, Products.product_name, Categories.category_name,Products.price,subcategories.subcategory_name,Products.description, Products.image
        FROM Products 
        JOIN Subcategories ON Products.subcategory_id = Subcategories.subcategory_id 
        JOIN Categories ON Subcategories.parent_category_id = Categories.category_id 
        ORDER BY products.product_id
        LIMIT $start_from, $results_per_page ";
$result = $conn->query($sql);

// Query to get total number of rows for pagination
$sql_count = "SELECT COUNT(*) AS total FROM Products";
$count_result = $conn->query($sql_count);
$row_count = $count_result->fetch_assoc();
$total_pages = ceil($row_count['total'] / $results_per_page);

?>

<!DOCTYPE html>
<html lang="en">

<head>


    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/tableheader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.0/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <title>E-Commerce Store</title>


</head>

<body>
    <?php

    // Check if userId exists in PHP session
    if (!isset($_SESSION['userId'])) {
    ?>
        <script>
            var userId = prompt("Please enter User Id:");
            if (!userId) {
                alert('Required User Id');
                window.location.reload(); //
            } else {
                // Send userId to server via AJAX
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: {
                        userId: userId
                    },
                    success: function(response) {
                        alert(response);
                        window.location.reload(); // Reload the page to reflect the changes
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });
            }
        </script>
    <?php } else { ?>
    <?php }

    ?>

    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid mt-4 ">
                <div class="card shadow rounded" style="margin:0xp;height: 44rem">
                    <div id="uId" style="display: flex;flex-direction: row-reverse;margin: 0px 10px -10px 0px;"></div>
                    <div class="table-title" style="margin: 21px;font-size: 25px;font-weight: bold;align-items: center;text-align: center;display: flex;border-bottom: 1px solid black;flex-wrap: wrap;align-content: center;flex-direction: row;justify-content: space-between;">E-Commerce Store
                        <a class="nav-link" data-widget="navbar-search" href="logout.php" role="button" id="logout-link">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>


                    <div class="" style="margin-top: 1rem !important;margin-top: 1rem !important;display: flex;flex-direction: row-reverse;align-content: center;">

                        <a onclick="" href="cart.php" class="btn btn-primary" style="margin: -20px 42px 0px 0px;">
                            <i class="fas fa-shopping-cart mr-2"></i>Checkout
                        </a>
                        <a onclick="" id="clearfilters" class="btn btn-primary" style="margin: -20px 42px 0px 0px;">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-wrapper" id="table-wrapper" style="max-height: 450px;">
                            <table class="table table-striped table-hover table-bordered  table-sm table-fixed" id="dataTable" style="height: 100%;">
                                <thead class="table-primary">
                                    <tr style="font-size: medium;">
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <!-- <br> -->
                                                <b>#</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">

                                                <b>Image</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <!-- <br> -->
                                                <div class="icon-containr">
                                                    <i class="fas fa-filter filter-icon"></i> <!-- Font Awesome filter icon -->
                                                    <i class="fas fa-chevron-up sort-icon" data-order="desc"></i> <!-- Font Awesome sort icon -->
                                                </div>
                                                <b>Name</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <!-- <br> -->
                                                <div class="icon-containr">
                                                    <i class="fas fa-filter filter-icon"></i> <!-- Font Awesome filter icon -->
                                                    <i class="fas fa-chevron-up sort-icon" data-order="desc"></i> <!-- Font Awesome sort icon -->
                                                </div>
                                                <b>Category</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <!-- <br> -->
                                                <div class="icon-containr">
                                                    <i class="fas fa-filter filter-icon"></i> <!-- Font Awesome filter icon -->
                                                    <i class="fas fa-chevron-up sort-icon" data-order="desc"></i> <!-- Font Awesome sort icon -->
                                                </div>
                                                <b>Sub-Category</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <!-- <br> -->
                                                <div class="icon-containr">
                                                    <i class="fas fa-filter filter-icon"></i> <!-- Font Awesome filter icon -->
                                                    <i class="fas fa-chevron-up sort-icon" data-order="desc"></i> <!-- Font Awesome sort icon -->
                                                </div>
                                                <b>Price</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">

                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <!-- <br> -->
                                                <div class="icon-containr">
                                                    <i class="fas fa-filter filter-icon"></i> <!-- Font Awesome filter icon -->
                                                    <i class="fas fa-chevron-up sort-icon" data-order="desc"></i> <!-- Font Awesome sort icon -->
                                                </div>
                                                <b>Description</b>
                                            </div>
                                        </th>
                                        <th class="text-nowrap comment-header align-middle">
                                            <div class="comment-title" style="display: flex;flex-wrap: nowrap;flex-direction: row-reverse;align-content: space-between;justify-content: space-around;align-items: center;">
                                                <b>Action</b>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="table-Body">
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $image = $row['image'];
                                            echo "<tr class='product-row' data-product-id='" . $row['product_id'] . "'>";
                                            echo "<td style=\"text-align:center\">" . $row['product_id'] . "</td>";
                                            echo "<td style=\"text-align:center\"><img src=\"" . $image . "\" alt=\"product image\" width=\"75\" height=\"75\"></td>";
                                            echo "<td>" . $row['product_name'] . "</td>";
                                            echo "<td>" . $row['category_name'] . "</td>";
                                            echo "<td>" . $row['subcategory_name'] . "</td>";
                                            echo "<td>$" . $row['price'] . "</td>";
                                            echo "<td>" . $row['description'] . "</td>";
                                            // echo "<td><a href='edit_product.php?id=" . $row['product_id'] . "' style=\"margin-right:10px\">Edit</a>";
                                            // echo "<a href='delete_product.php?id=" . $row['product_id'] . "'>Delete</a></td>";
                                            echo "<td style=\"text-align:center\"><a href='#' style=\"margin-right:10px\">Edit</a>";
                                            echo "<a href='#'>Delete</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No products found</td></tr>";
                                    }


                                    ?>
                                </tbody>
                            </table>
                            <div class="pagination">
                                <?php
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    if ($i == $page) {
                                        echo "<a href='?page=$i' class='active'>$i</a> ";
                                    } else {
                                        echo "<a href='?page=$i'>$i</a> ";
                                    }
                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="filter-dropdown">
        <div class="filter-header">
            <label>Select Filters:</label>
        </div>
        <label class=""><input type="checkbox" id="select-all-checkbox"> Select All</label><br>
        <input type="text" id="filter-search" placeholder="Search..."><br>
        <div class="filter-options">
        </div>
        <div class="row">
            <div class="col-sm-4">
                <button class="ok-button">Ok</button>
            </div>
            <div class="col-sm-4">
                <button class="cancel-button">Cancel</button>
            </div>
        </div>
    </div>
    <script src="../js/sorting.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/index.js"></script>

</body>

</html>