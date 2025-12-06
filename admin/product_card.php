<?php
require_once '../includes/autoloader.php';
session_start();

$db = new Database();
$adminController = new AdminController($db);
$productModel = new Product($db);

$message = '';
$productToEdit = null;

if (!AuthManager::isAdmin()) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $message = $adminController->handleProductCrud($_POST, $_FILES);
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $productToEdit = $productModel->read($id);
    if (!$productToEdit) {
        $message = "Error: Product ID not found.";
    }
}

$allProducts = $productModel->readAll();
$categories = $db->query("SELECT * FROM Category")->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>DevTeam Voltmart Admin - Product Management</title>
</head>

<body>
    <?php include '../includes/header.php';
    ?>

    <h2>Product Management (CRUD)</h2>
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="product_crud.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $productToEdit ? 'update' : 'create'; ?>">
        <input type="hidden" name="productID" value="<?php echo $productToEdit->productID ?? ''; ?>">
        <input type="hidden" name="currentImagePath" value="<?php echo $productToEdit->imagePath ?? ''; ?>">

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($productToEdit->name ?? $_POST['name'] ?? ''); ?>" required><br>

        <label for="categoryID">Category:</label>
        <select name="categoryID" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['categoryID']; ?>"
                    <?php
                    $selectedCat = $productToEdit->categoryID ?? $_POST['categoryID'] ?? 0;
                    if ($selectedCat == $cat['categoryID']) echo 'selected';
                    ?>>
                    <?php echo $cat['categoryName']; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="productPhoto">Photo Upload:</label>
        <input type="file" name="productPhoto">
        <?php if ($productToEdit && $productToEdit->imagePath): ?>
            <p>Current Image: <img src="../<?php echo $productToEdit->imagePath; ?>" style="width: 50px;" alt="Current Product Photo"></p>
        <?php endif; ?><br>

        <label for="altText">Alt Text (AODA):</label>
        <input type="text" name="altText" value="<?php echo htmlspecialchars($productToEdit->altText ?? $_POST['altText'] ?? ''); ?>" required><br>

        <button type="submit"><?php echo $productToEdit ? 'Update Product' : 'Create Product'; ?></button>
    </form>

    <h3>Existing Products</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allProducts as $product): ?>
                <tr>
                    <td><?php echo $product['productID']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['categoryName']; ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stockQuantity']; ?></td>
                    <td>
                        <a href="product_crud.php?action=edit&id=<?php echo $product['productID']; ?>">Edit</a> |
                        <form style="display:inline;" method="POST" action="product_crud.php">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="productID" value="<?php echo $product['productID']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php include '../includes/footer.php';
    ?>
</body>

</html>