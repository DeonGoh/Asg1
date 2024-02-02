<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

if(isset($_SESSION["OrderID"])) {	
	echo "<div style='width:80%; margin:auto;'>";
	echo "<p>Checkout successful. Your order number is $_SESSION[OrderID]</p>";

	$items = $_SESSION['Items'];

	echo "<table class='producttable'>";
	echo "<tr>";
	echo "<th class='tablerow'>Product ID</th>";
	echo "<th class='tablerow'>Product Name</th>";
	echo "<th class='tablerow'>Product Price(S$)</th>";
	echo "<th class='tablerow'>Product Quantity</th>";
	echo "</tr>";

	foreach ($items as $item) {
		$productId = $item['productId'];
		$name = $item['name'];
		$price = $item['price'];
		$quantity = $item['quantity'];
		
		// Use the values as needed
		echo "<tr>";
		echo "<td class='tablerow'>$productId</td>";
		echo "<td class='tablerow'>$name</td>";
		echo "<td class='tablerow'>$price</td>";
		echo "<td class='tablerow'>$quantity</td>";
		echo "</tr>";
	}
	echo "</table>";

	echo "<p>Subtotal : S$ ". number_format($_SESSION['SubTotal'],2). "</p>";
	echo "<p>GST as of ". date_format(new DateTime(), "Y-m-d") . " : $_SESSION[TaxRate]%</p>";
	echo "<p>Tax total : S$". number_format($_SESSION['Tax'],2). "</p>";
	echo "<p>Delivery Type : $_SESSION[DeliveryType]</p>";
	echo "<p>Delivery Charge : S$". number_format( $_SESSION['DeliveryCharge'],2) . "</p>";
	echo "<p>Shipping to $_SESSION[ShipAddress] </p>";
	echo "<p>Total including tax : ". number_format($_SESSION['Total'],2). "</p>";
	echo "<p></p>";
	echo "<p>Thank you for your purchase.&nbsp;&nbsp;";
	echo '<a href="index.php">Continue shopping</a></p>';
} 

include("footer.php"); // Include the Page Layout footer
?>