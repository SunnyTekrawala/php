<?php
// checkout.php
require_once 'includes/autoloader.php';

$db = new Database();
$cartManager = new CartManager();
$pdfGenerator = new PDFGenerator();
$authManager = new AuthManager($db);

// Check if cart is empty
if (empty($cartManager->getCartItems())) {
    header('Location: cart.php');
    exit;
}

// Check if user is logged in (Registration form before checkout)
if (!AuthManager::isLoggedIn()) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$message = '';
$invoicePath = '';
$userID = $_SESSION['user_id'];
$cartItems = $cartManager->getCartItems();
$cartTotal = $cartManager->getCartTotal();

// Process the order upon confirmation (e.g., hitting a final 'Place Order' button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        // 1. Store Order in Database (Transaction required for integrity)
        $db->query("START TRANSACTION");

        // Order Model (Assuming Order class exists for database operations)
        $sqlOrder = "INSERT INTO `Order` (userID, totalAmount, orderStatus) VALUES (:userID, :total, 'pending')";
        $db->query($sqlOrder, ['userID' => $userID, 'total' => $cartTotal]);
        $orderID = (int)$db->lastInsertId();

        // 2. Store Order Details (Line Items)
        $sqlDetail = "INSERT INTO OrderDetail (orderID, productID, quantity, unitPrice) VALUES (:oID, :pID, :qty, :uPrice)";
        foreach ($cartItems as $item) {
            $db->query($sqlDetail, [
                'oID' => $orderID,
                'pID' => $item['id'],
                'qty' => $item['quantity'],
                'uPrice' => $item['price']
            ]);
        }

        // 3. Generate PDF Invoice
        $userDetails = (new User($db))->read($userID);
        $invoicePath = $pdfGenerator->generateInvoice(['orderID' => $orderID, 'total' => $cartTotal], $userDetails, $cartItems);

        // 4. Update Order with Invoice Path
        if ($invoicePath) {
            $sqlUpdate = "UPDATE `Order` SET invoicePath = :path, orderStatus = 'shipped' WHERE orderID = :oID";
            $db->query($sqlUpdate, ['path' => $invoicePath, 'oID' => $orderID]);
        }

        $db->query("COMMIT");
        $cartManager->clearCart(); // Empty the session cart
        $message = "Order successfully placed! Your invoice is ready.";
    } catch (\PDOException $e) {
        $db->query("ROLLBACK");
        error_log("Checkout Error: " . $e->getMessage());
        $message = "An error occurred during checkout. Please try again.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Voltmart - Checkout</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <h2>Checkout Summary</h2>

    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
        <?php if ($invoicePath): ?>
            <p>Download your invoice: <a href="<?php echo $invoicePath; ?>" target="_blank">Invoice #<?php echo $orderID; ?></a></p>
        <?php endif; ?>
    <?php else: ?>
        <h3>Items: <?php echo count($cartItems); ?></h3>
        <h3>Total: $<?php echo number_format($cartTotal, 2); ?></h3>

        <form method="POST" action="checkout.php">
            <input type="hidden" name="place_order" value="1">
            <button type="submit">Place Order and Get Invoice</button>
        </form>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>
</body>

</html>