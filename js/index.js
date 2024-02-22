$(document).ready(function () {
  // Handle click event on table rows
  $(".product-row").click(function () {
    var productId = $(this).data("product-id");
    // Redirect to product details page with product ID as query parameter
    window.location.href = "product_details.php?id=" + productId;
  });
});
