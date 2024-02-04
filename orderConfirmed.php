<link rel="stylesheet" href="css/site.css">

<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

if(isset($_SESSION["OrderID"])) {	
	echo "<p>Checkout successful. Your order number is $_SESSION[OrderID]</p>";

	$items = $_SESSION['Items'];


	echo "Your purchased items were";
	echo "";
	echo "<table style='width:100%;border-collapse:collapse; '>";
	echo "<tr>";
	echo "<th style='width:25%; background-color: #3366ff; color: white;'>Product ID</th>";
	echo "<th style='width:25%; background-color: #3366ff; color: white;'>Product Name</th>";
	echo "<th style='width:25%; background-color: #3366ff; color: white;'>Product Price(S$)</th>";
	echo "<th style='width:25%; background-color: #3366ff; color: white;'>Product Quantity</th>";
	echo "</tr>";

	foreach ($items as $item) {
		$productId = $item['productId'];
		$name = $item['name'];
		$price = $item['price'];
		$quantity = $item['quantity'];
		
		// Use the values as needed
		echo "<tr style='background-color:#e6e6e6'>";
		echo "<td style='width:25%;'>$productId</td>";
		echo "<td style='width:25%;'>$name</td>";
		echo "<td style='width:25%;'>$price</td>";
		echo "<td style='width:25%;'>$quantity</td>";
		echo "</tr>";
	}
	echo "</table>";

	echo "<p>Subtotal : S$ ". number_format($_SESSION['SubTotal'],2). "</p>";
	echo "<p>GST as of ". date_format(new DateTime(), "Y-m-d") . " : $_SESSION[TaxRate]%</p>";
	echo "<p>Tax total : S$". number_format($_SESSION['Tax'],2). "</p>";
	echo "<p>Delivery Type : $_SESSION[DeliveryType]</p>";
	echo "<p>Delivery Charge : S$". number_format( $_SESSION['DeliveryCharge'],2) . "</p>";
	echo "<p>Shipping to : $_SESSION[ShipAddress] </p>";
	echo "<p>Total including tax : S$". number_format($_SESSION['Total'],2). "</p>";
	echo "<p>Estimated shipping date : ". date_format(new DateTime(), "Y-m-d") . " - ". date_format($_SESSION['DeliveryDate'], "Y-m-d")."</p>";
	echo "<p></p>";
	echo "<p>Thank you for your purchase.&nbsp;&nbsp;";
	echo '<a href="index.php">Continue shopping</a></p>';
} 

include("footer.php"); // Include the Page Layout footer
?>
