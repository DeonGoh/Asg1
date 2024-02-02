<?php
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php");
?>
<img src="Images/Banner.png" class="img-fluid" style="display:block; margin:auto;" />";

<?php

if (!isset($_SESSION["ShopperID"])) {
     echo "<form action='initialization.php' method='post'>";
     echo "<input type='hidden' name='action' value='lg' />";
     echo "&nbsp;&nbsp;<button type='submit'>Quick Login as User 1</button>";
     echo "</form>";
} else {
     echo "<p>Product 1</p>";
     echo "<form action='cartFunctions.php' method='post'>";
     echo "<input type='hidden' name='action' value='add' />";
     echo "<input type='hidden' name='product_id' value='1' />";
     echo "Quantity: <input type='number' name='quantity' value='1' min='1' max='10' style='width:40px' required />";
     echo "&nbsp;&nbsp;<button type='submit'>Add to Cart</button>";
}
// Include the Page Layout footer
include("footer.php");
?>