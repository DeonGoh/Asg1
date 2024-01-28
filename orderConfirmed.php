<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

if(isset($_SESSION["OrderID"])) {	
	echo "<p>Checkout successful. Your order number is $_SESSION[OrderID]</p>";
	echo "<p>Subtotal : S$ ". number_format($_SESSION['SubTotal'],2). "</p>";
	echo "<p>GST as of ". date_format(new DateTime(), "Y-m-d") . " : $_SESSION[TaxRate]%</p>";
	echo "<p>Tax total : S$ $_SESSION[Tax]</p>";
	echo "<p>Delivery Type : S$ $_SESSION[DeliveryType]</p>";
	echo "<p>Delivery Charge : S$ $_SESSION[DeliveryCharge]</p>";
	echo "<p>Total including tax : </p>";
	echo "<p></p>";
	echo "<p>Thank you for your purchase.&nbsp;&nbsp;";
	echo '<a href="index.php">Continue shopping</a></p>';
} 

include("footer.php"); // Include the Page Layout footer
?>
