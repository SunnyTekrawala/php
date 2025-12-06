<?php
// products.php
session_start();
require_once 'includes/autoloader.php';

// Initialize core classes
try {
    $db = new Database();
    $productModel = new Product($db);
    $cartManager = new CartManager($db);
} catch (Exception $e) {
    // Handle database connection error gracefully
    die("Error connecting to the database: " . $e->getMessage());
}

$pageTitle = "All Products";
$message = '';

// --- Handle Add to Cart POST Request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCart') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?? 1;

    if ($productId > 0 && $quantity > 0) {
        if ($cartManager->addItem($productId, $quantity)) {
            $message = "Product successfully added to your <a href='cart.php'>cart</a>!";
        } else {
            $message = "Error: Could not add product to cart or product not found.";
        }
    }
}

// --- Fetch all products from the database ---
$products = $productModel->getAllProducts();

// Includes the header and navbar
include 'includes/header.php';
?>

<style>
    /* Add basic styling for the product grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }

    .product-card {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
    }

    .product-card h3 {
        color: #333;
        margin-top: 0;
    }

    .product-card .price {
        font-size: 1.2em;
        color: #007bff;
        font-weight: bold;
        margin: 10px 0;
    }

    .product-card button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: auto;
        /* Push button to the bottom */
    }

    .product-card button:hover {
        background-color: #218838;
    }

    .message {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>

<h2>Our Electric Goods</h2>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (empty($products)): ?>
    <p>Sorry, there are no products available at this time.</p>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                <p style="color: <?php echo $product['stock'] > 0 ? 'green' : 'red'; ?>;">
                    <?php echo $product['stock'] > 0 ? 'In Stock: ' . $product['stock'] : 'Out of Stock'; ?>
                </p>

                <?php if ($product['stock'] > 0): ?>
                    <form action="products.php" method="POST">
                        <input type="hidden" name="action" value="addToCart">
                        <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                        <label for="qty_<?php echo $product['id']; ?>">Qty:</label>
                        <input type="number" id="qty_<?php echo $product['id']; ?>" name="quantity" value="1" min="1" max="<?php echo (int)$product['stock']; ?>" style="width: 50px;">
                        <button type="submit">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <button disabled>Out of Stock</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
// Includes the footer
include 'includes/footer.php';
?>