document.addEventListener("DOMContentLoaded", function () {
  var quantityElement = document.getElementById("quantity");
  var incrementButton = document.querySelector(".increment");
  var decrementButton = document.querySelector(".decrement");

  // Increment quantity
  incrementButton.addEventListener("click", function () {
    var quantity = parseInt(quantityElement.textContent);
    quantityElement.textContent = quantity + 1;
  });

  // Decrement quantity (minimum quantity is 1)
  decrementButton.addEventListener("click", function () {
    var quantity = parseInt(quantityElement.textContent);
    if (quantity > 1) {
      quantityElement.textContent = quantity - 1;
    }
  });

  // Add to cart functionality can be added here
});
// Assuming you have a function to handle form submission
function addToCart(productId, quantity) {
  $.ajax({
    type: "POST",
    url: "",
    data: {
      method: "addCart",
      product_id: productId,
      quantity: quantity,
    },
    success: function (response) {
      alert(response);
      window.location.href = "index.php";
    },

    error: function (jqXHR, textStatus, errorThrown) {
      console.error("AJAX Error:", textStatus, errorThrown);
      var error = new Error(errorThrown);
    },
  });
}
