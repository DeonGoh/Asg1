<?php

use function PHPSTORM_META\elementType;

session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- HTML Form to collect search keyword and submit it to the same page in server -->
<div style="width:80%; margin:auto;">
    <!-- Container -->
    <form name="frmSearch" method="get" action="">
        <!-- Product Search -->
        <div class="mb-3 row">
            <!-- 1st row -->
            <div class="col-sm-9 offset-sm-3">
                <span class="page-title">Product Search</span>
            </div>
        </div> <!-- End of 1st row -->
        <div class="mb-3 row">
            <!-- 2nd row -->
            <label for="keywords" class="col-sm-3 col-form-label">Product Title:</label>
            <div class="col-sm-9">
                <input class="form-control" name="keywords" id="keywords" type="search" />
            </div>
        </div> <!-- End of 2nd row -->
        <!-- Price Range -->
        <div class="mb-3 row">
            <label for="price_range" class="col-sm-3 col-form-label">Price Range:</label>
            <div class="col-sm-6">
                <div class="price_range">
                    <input class="form-control" name="min_price" id="price_range" type="number"
                        placeholder="minimum price" value="1" />
                    <div> to </div>
                    <input class="form-control" name="max_price" id="price_range" type="number"
                        placeholder="maximum price" value="1000" />
                </div>
            </div>
            <div class="col-sm-3" style="text-align: center;">
                <button type="submit">Search</button>
            </div>
        </div>
    </form>

    <?php
    // Include the PHP file that establishes database connection handle: $conn
    include_once("mysql_conn.php");

    // The non-empty search keyword is sent to server
    if ( // if keyword and price range are both declared
        isset($_GET["keywords"]) && trim($_GET['keywords']) != "" ||
        isset($_GET["min_price"]) && trim($_GET['min_price']) != "" ||
        isset($_GET["max_price"]) && trim($_GET['max_price']) != ""
    ) {
        // validate price range: that the min price is smaller than max price
        if ($_GET["min_price"] != "" && $_GET["max_price"] != "" && ($_GET["min_price"] > $_GET["max_price"])) {
            echo "<h4 style='text-align:center; color:red;'>Min Price Higher than Max Price</h3>";
        } else {
            $qry = "SELECT * FROM product WHERE ProductTitle LIKE ? AND 
                    (Price BETWEEN ? AND ? OR
                    OfferedPrice BETWEEN ? AND ?)  
                    ORDER BY ProductTitle ASC";
            $search_string = "%" . $_GET["keywords"] . "%";
            $stmt = $conn->prepare($qry);
            $stmt->bind_param("sdddd", $search_string, $_GET["min_price"], $_GET["max_price"], $_GET["min_price"], $_GET["max_price"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            // Retrieve list of product records with "ProductTitle" 
            // contains the keyword entered by shopper, and display them in a table.
            echo "<p style='font-weight: bold;'>Search for: '$_GET[keywords]' and Price Range of $_GET[min_price]-$_GET[max_price]</p>";
            while ($row = $result->fetch_array()) {
                $product = "productDetails.php?pid=$row[ProductID]";
                echo "<p><a href=$product>$row[ProductTitle]</a></p>";
            }
        }
    }
    echo "</div>"; // End of container
    include("footer.php"); // Include the Page Layout footer
    ?>