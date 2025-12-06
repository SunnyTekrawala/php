<?php
// cart.php
require_once 'includes/autoloader.php';

$cartManager = new CartManager();
$db = new Database(); // Needed if fetching product details dynamically

// Handle updates (Update/Remove logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_action'])) {
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($_POST['cart_action'] === 'update' && isset($_POST['quantity'])) {
        $newQuantity = (int)$_POST['quantity'];
        $cartManager->updateQuantity($productId, $newQuantity);
    } elseif ($_POST['cart_action'] === 'remove') {
        $cartManager->removeFromCart($productId);
    }
    // Redirect to prevent form resubmission
    header('Location: cart.php');
    exit;
}

$cartItems = $cartManager->getCartItems();
$cartTotal = $cartManager->getCartTotal();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Voltmart - Shopping Cart</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <h2>Your Shopping Cart</h2>

    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty. Start shopping!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form style="display:inline;" method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="cart_action" value="update">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width: 50px;" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <form style="display:inline;" method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="cart_action" value="remove">
                                <button type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Cart Total: $<?php echo number_format($cartTotal, 2); ?></h3>

        <a href="product_list.php">Continue Shopping</a>
        <a href="checkout.php">Proceed to Checkout</a>

    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>
</body>

</html>