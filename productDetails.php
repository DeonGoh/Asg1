<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 90% width of viewport -->
<div style='width:90%; margin:auto;'>

    <?php
    $pid = $_GET["pid"]; // Read Product ID from query string

    // Include the PHP file that establishes database connection handle: $conn
    include_once("mysql_conn.php");
    $qry = "SELECT * from product where ProductID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $pid);     // "i" - integer 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // To Do 1:  Display Product information. Starting ....
    while ($row = $result->fetch_array()) {
        // Display page Header -
        // Product's name is read from the "ProductTitle" column of "Product" table
        echo "<div class='row'>";
        echo "<div class='col-sm-12' style='padding:5px;'>";
        echo "<span class='page-title'>$row[ProductTitle]</span>";
        echo "</div>";
        echo "</div>";

        echo "<div class='row'>"; // Start a new row

        // Left Column - displaay the product's description,
        echo "<div class='col-sm-9' style='padding:5px;'>";
        echo "<p>$row[ProductDesc]</p>";

        // Left column - Display the product's Specification,
        $qry = "SELECT s.SpecName, ps.SpecVal from productspec ps
            INNER JOIN specification s ON ps.SpecID=s.SpecID
            WHERE ps.ProductID=?
            ORDER BY ps.priority";

        $stmt = $conn->prepare($qry);
        $stmt->bind_param("i", $pid); // "i" - integer
        $stmt->execute();
        $result2 = $stmt->get_result();
        $stmt->close();

        while ($row2 = $result2->fetch_array()) {
            echo $row2["SpecName"] . ": " . $row2["SpecVal"] . "<br/ >";
        }

        echo "</div>"; // End of left column

        // Right column - display the product's image
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-sm-3 style='vertical-align:top; padding:5px;'>";
        echo "<p><img src=$img style='width: 100%;height: auto;'/></p>";

        // Right Column - display the product's price
        // check if product is on offer
        $offeredPrice = number_format($row["OfferedPrice"], 2);
        $offered = $row["Offered"];
        $formattedPrice = number_format($row["Price"], 2);
        if ($offered == 1) { // on offer
            echo "Price:<span style='font-weight:bold; color:red;'>
            <s>S$ $formattedPrice</s> Offer: S$ $offeredPrice</span>";
        } else { // not on offer
            echo "Price:<span style='font-weight:bold; color:red;'>
          S$ $formattedPrice</span>";
        }
    }

    // To Do 1:  Ending ....

    // check if the product is out of stock
    $qry = "SELECT Quantity FROM Product WHERE ProductId = ? ";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $pid); // "i" - integer
    $stmt->execute();
    $result3 = $stmt->get_result();
    $stmt->close();
    while ($row3 = $result3->fetch_array()) {
        if ($row3["Quantity"] <= 0) {
            $product_out_of_stock = true;
        } else {
            $product_out_of_stock = false;
        }
    }

    // To Do 2:  Create a Form for adding the product to shopping cart. Starting ....
    echo "<form action='cartFunctions.php' method='post'>";
    echo "<input type='hidden' name='action' value='add' />";
    echo "<input type='hidden' name='product_id' value='$pid' />";
    // if the product is in stock display form
    if ($product_out_of_stock == false) { // if in stock
        echo "Quantity: <input type='number' name='quantity' value='1' 
      min='1' max='10' style='width:40px' required />";

        echo "<button type='submit'>Add to Cart</button>";
    } else { // not in stock
        echo "Quantity: <input type='number' name='quantity' value='1' 
      min='1' max='10' style='width:40px' required />";

        echo "<button type='submit' disabled>Add to Cart</button>";
        echo "<p style='color: red;'>Out of Stock</p>";
    }

    echo "</form>";
    echo "</div>"; // End of right column
    echo "</div>"; // End of row

    // To Do 2:  Ending ....

    $conn->close(); // Close database connnection
    echo "</div>"; // End of container
    include("footer.php"); // Include the Page Layout footer
    ?>