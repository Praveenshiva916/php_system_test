<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add To Cart</title>
    <link href="style.css" rel="stylesheet">
    <script src="script.js"></script>
</head>
<body>
    <span id ="id1"></span>
    <h1 class="disc">Discount Table</h1>
   <table class="disc_table">
           <thead>
            <tr>
                <td>Vendor</td>
                <td>Trade A</td>
                <td>Trade B</td>
                <td>Trade C</td>
                <td>Trade D</td>

            </tr>
           </thead>
           <tbody>
           <tr>
                <td>Vendor 1</td>
                <td>12</td>
                <td>12</td>
                <td>N/A</td>
                <td>6</td>

             </tr>
             <tr>
                <td>Vendor 2</td>
                <td>10</td>
                <td>8</td>
                <td>20</td>
                <td>N/A</td>

             </tr>
             <tr>
                <td>Vendor 3</td>
                <td>N/A</td>
                <td>25</td>
                <td>3</td>
                <td>16</td>

             </tr>
             <tr>
                <td>Vendor 4</td>
                <td>9</td>
                <td>N/A</td>
                <td>16</td>
                <td>30</td>

             </tr>
             <tr>
                <td>Vendor 5</td>
                <td>5</td>
                <td>11</td>
                <td>N/A</td>
                <td>30</td>

             </tr>
          
           </tbody>
   </table>





<?php
session_start();

$discountTable = array(
    'Vendor 1' => array('Trade A' => 12, 'Trade B' => 12, 'Trade D' => 6),
    'Vendor 2' => array('Trade A' => 10, 'Trade B' => 8, 'Trade C' => 20),
    'Vendor 3' => array('Trade B' => 25, 'Trade C' => 3, 'Trade D' => 16),
    'Vendor 4' => array('Trade A' => 9, 'Trade C' => 16, 'Trade D' => 30),
    'Vendor 5' => array('Trade A' => 5, 'Trade B' => 11, 'Trade D' => 30)
);


$productTable = array(
    array('Product Name' => 'Test Product 1', 'Price' => 12.5, 'Tags' => 'Trade A,ice', 'Vendor' => 'Vendor 5'),
    array('Product Name' => 'Test Product 2', 'Price' => 42.5, 'Tags' => 'Trade B,ice2', 'Vendor' => 'Vendor 4'),
    array('Product Name' => 'Test Product 3', 'Price' => 200, 'Tags' => 'Trade C,test', 'Vendor' => 'Vendor 2'),
    array('Product Name' => 'Test Product 4', 'Price' => 52.5, 'Tags' => 'Trade C,test', 'Vendor' => 'Vendor 2'),
    array('Product Name' => 'Test Product 5', 'Price' => 67, 'Tags' => 'Trade D,test', 'Vendor' => 'Vendor 5')
);

if (!isset($_SESSION['shoppingCart'])) {
    $_SESSION['shoppingCart'] = array();
}


function applyDiscount($price, $tags, $vendor, $discountTable) {
    $tagArray = explode(',', $tags); 

    $discounts = array();
    foreach ($tagArray as $tag) {
        if (isset($discountTable[$vendor][$tag])) {
            $discounts[] = $discountTable[$vendor][$tag];
        }
    }

    // Calculate the minimum discount percentage
    $minDiscount = (count($discounts) > 0) ? min($discounts) : 0;

    // Calculate the discount price
    $discountedPrice = $price - ($price * $minDiscount / 100);

    return array('Discount Price' => number_format($discountedPrice, 2), 'Discount Percentage' => $minDiscount);
}

if (isset($_POST['addToCart'])) {
    $productId = $_POST['productId'];
    $product = $productTable[$productId];
    $discountInfo = applyDiscount($product['Price'], $product['Tags'], $product['Vendor'], $discountTable);

    $product['Discount Price'] = $discountInfo['Discount Price'];
    $product['Discount Percentage'] = $discountInfo['Discount Percentage'];

    $_SESSION['shoppingCart'][] = $product;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dynamic Product Table with Shopping Cart</title>
</head>
<body>
    
    <h1 class="disc">Product Table</h1>
    <table class="disc_table">
        <thead>
            <tr>
                <td>#</td>
                <td>Product Name</td>
                <td>Price</td>
                <td>Tags</td>
                <td>Vendor</td>
                <td>Discount Percentage</td>
                <td>Discount Price</td>
                <td>Add To Cart</td>
            </tr>
        </thead>
        <tbody class="prod-body">
            <?php
            foreach ($productTable as $key => $product) {
                echo "<form method='post'>";
                echo "<tr>";
                echo "<td>" . ($key + 1) . "</td>";
                echo "<td>" . $product['Product Name'] . "</td>";
                echo "<td>" . $product['Price'] . "</td>";
                echo "<td>" . $product['Tags'] . "</td>";
                echo "<td>" . $product['Vendor'] . "</td>";
                $discountInfo = applyDiscount($product['Price'], $product['Tags'], $product['Vendor'], $discountTable);
                echo "<td>" . $discountInfo['Discount Percentage'] . "%</td>";
                echo "<td>" . $discountInfo['Discount Price'] . "</td>";
                echo "<td><input type='hidden' name='productId' value='$key'><input type='submit' name='addToCart' value='Add To Cart'></td>";
                echo "</tr>";
                echo "</form>";
            }
            ?>
        </tbody>
    </table>

    <h1 class="disc">Shopping Cart</h1>
    <table class="disc_table">
        <thead>
            <tr>
                <td>#</td>
                <td>Product Name</td>
                <td>Org Price</td>
                <td>Discount Percentage</td>
                <td>Discount Price</td>
                <td>Remove Cart</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalValue = 0;
            $shoppingCart = isset($_SESSION['shoppingCart']) ? $_SESSION['shoppingCart'] : array();
            if (isset($_POST['removeFromCart'])) {
                $removeProductId = $_POST['removeProductId'];
                if (isset($_SESSION['shoppingCart'][$removeProductId])) {
                
                    unset($_SESSION['shoppingCart'][$removeProductId]);
                }
            
                $_SESSION['shoppingCart'] = array_values($_SESSION['shoppingCart']);
            }
            
            foreach ($shoppingCart as $key => $item) {
                $totalValue += $item['Discount Price'];
                echo "<tr>";
                echo "<td>" . ($key + 1) . "</td>";
                echo "<td>" . $item['Product Name'] . "</td>";
                echo "<td>" . $item['Price'] . "</td>";
                echo "<td>" . $item['Discount Percentage'] . "%</td>";
                echo "<td>" . $item['Discount Price'] . "</td>";
                echo "<td><form method='post'><input type='hidden' name='removeProductId' value='$key'><input type='submit' name='removeFromCart' value='Remove'></form></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <p>Total Value: $<?php echo number_format($totalValue, 2); ?></p>
</body>
</html>
