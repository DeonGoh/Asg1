<?php
// Include the code that contains shopping cart's functions.
// Current session is detected in "cartFunctions.php, hence need not start session here.
include_once("cartFunctions.php");
include("header.php"); // Include the Page Layout header

if (!isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header("Location: login.php");
	exit;
}

echo "<div id='myShopCart' style='margin:auto'>"; // Start a container
if (isset($_SESSION["Cart"])) {
	include_once("mysql_conn.php");
	// Retrieve from database and display shopping cart in a table
	$qry = "SELECT sci.*,(sci.Price*sci.Quantity) AS Total,p.Offered,p.OfferedPrice FROM ShopCartItem sci 
			INNER JOIN product p ON sci.ProductID = p.ProductID WHERE ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("i", $_SESSION["Cart"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

	if ($result->num_rows > 0) { 
		// Format and display the page header and header row of shopping cart page
		echo "<p class='page-title' style='text-align:center'>Shopping Cart</p>";
		echo "<div class='table-responsive' style='width:80%; margin:0 auto 0 auto;'>"; // Bootstrap responsive table
		echo "<table class='table table-hover'>"; // Start of table
		echo "<thead class='cart-header'>"; // Start of table's header section
		echo "<tr>"; // Start of header row
		echo "<th width='250px'>Item</th>";
		echo "<th width='90px'>Price</th>";
		echo "<th width='60px'>Quantity</th>";
		echo "<th width='120px'>Total (S$)</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>"; // End of header row
		echo "</thead>"; // End of table's header section

		// Declare an array to store the shopping cart items in session variable 
		$_SESSION['Items'] = array();

		// Display the shopping cart content
		$subTotal = 0; // Declare a variable to compute subtotal before tax
		echo "<tbody>"; // Start of table's body section
		while ($row = $result->fetch_array()) {
			echo "<tr>";
			echo "<td style='width:50%'>$row[Name]<br />";
			echo "Product ID: $row[ProductID]</td>";
			$offered = $row["Offered"];
			$offeredPrice = number_format($row["OfferedPrice"], 2);
			$formattedPrice = number_format($row["Price"], 2);
			if ($offered == 1) { // if on offer
				echo "<td><s>$formattedPrice</s><br>
				<span style='color:red;'>$offeredPrice</span></td>";
			} else { // not on offer
				echo "<td> $formattedPrice</td>";
			}
			echo "<td>";
			echo "<form action='cartFunctions.php' method='post'>";
			echo "<select name='quantity' onChange='this.form.submit()'>";
			for ($i = 1; $i <= 10; $i++) {
				if ($i == $row["Quantity"]) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				echo "<option value='$i' $selected>$i</option>";
			}
			echo "</select>";
			echo "<input type = 'hidden' name='action' value='update' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]'/>";
			echo "</form>";
			echo "</td>";

			$formattedTotal = number_format($row["Total"], 2);
			$offeredTotal = number_format($offeredPrice * $row["Quantity"], 2);
			if ($offered == 1) { // if on offer
				echo "<td><s>$formattedTotal</s><br>
				<span style='color:red;'>$offeredTotal</span></td>";
			} else { // not on offer
				echo "<td>$formattedTotal</td>";
			}
			echo "<td>"; // column for remive item form shopping cart
			echo "<form action ='cartFunctions.php' method='post'>";
			echo "<input type ='image' src='images/trash-can.png' alt='Submit'/> ";
			echo "<input type ='hidden' name ='product_id' value='$row[ProductID]'/>";
			echo "<input type ='hidden' name='action' value='remove'/>";
			echo "</form>";
			echo "</td>";
			echo "</tr>";
			// Store the shopping cart items in session variable as an associate array
			if ($offered == 1) {
				$price = $offeredPrice;
			}
			else{
				$price = $row['Price'];
			}
			$_SESSION['Items'][] = array(
				"productId" => $row["ProductID"],
				"name" => $row["Name"],
				"price" => $price,
				"quantity" => $row['Quantity']
			);

			// Accumulate the running sub-total
			if ($offered == 1) { // if on offer
				$subTotal += $offeredTotal;
			} else { // not on offer
				$subTotal += $row["Total"];
			}
		}
		echo "</tbody>"; // End of table's body section
		echo "</table>"; // End of table
		echo "</div>"; // End of Bootstrap responsive table

		echo "<form action ='cartFunctions.php' method='post'>";
		echo "<p style='text-align:right;'>";
		echo "Delivery method ";
		echo "<select name='deliveryMethod' onChange='this.form.submit()'>";
		if ($_SESSION['DeliveryType'] == 'Express') {
			echo "<option value='Normal'>Normal Delivery</option>";
			echo "<option value='Express' selected>Express Delivery</option>";
		} else {
			$_SESSION['DeliveryType'] = 'Normal';
			echo "<option value='Normal' selected>Normal Delivery</option>";
			echo "<option value='Express'>Express Delivery</option>";
		}
		echo "</select>";
		echo "<input type ='hidden' name='action' value='deliver'/>";
		echo "</p>";
		echo "</form>";

		//===================
		// This run once when the page is loadedcause the variables will not be loaded otherwise
		//===================
		switch ($_SESSION['DeliveryType']) {
			case 'Normal':
				$deliveryDate = addWorkingDays(date("Y-m-d"), 2);
				$deliveryType = "Normal";
				$charge = 5;
				break;
			case 'Express':
				$charge = 10;
				if ($_SESSION["SubTotal"] > 200) {
					$charge = 0;
				}
				$deliveryType = "Express";
				$deliveryDate = date_add(new DateTime(), date_interval_create_from_date_string("1 days"));
				break;
		}
		$_SESSION['DeliveryDate'] = $deliveryDate;
		$_SESSION['DeliveryCharge'] = $charge;
		$_SESSION['DeliveryType'] = $deliveryType;
		
		echo "<p style='text-align: right;'>Normal delivery will deliver your items within 2 working days after an order is placed </p>";
		echo "<p style='text-align: right;'>Express delivery will delivered your items within 24 hours after an order is placed</p>";
		// To Do 4 (Practical 4): 
		// Display the subtotal at the end of the shopping cart
		echo "<p style='text-align:right; font: size 20px'> Subtotal = S$" . number_format($subTotal, 2);
		$_SESSION["SubTotal"] = round($subTotal, 2);

		echo "<p style='text-align: right; font: size 20px'> Shipping Charge = S$" . number_format($_SESSION["DeliveryCharge"], 2);

		
		echo "<p style='text-align:right;'>Estimated shipping date : " . date_format(new DateTime(), "Y-m-d") . " - " . date_format($_SESSION['DeliveryDate'], "Y-m-d") . "</p>";
		
		// Add PayPal Checkout button on the shopping cart page
		echo "<form method='post' action='checkoutProcess.php'>";
		echo "<input type='image' style='float:right;'
					src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'>";
		echo "</form></p>";
	} else {
		echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
	}
	$conn->close(); // Close database connection
} else {
	echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer