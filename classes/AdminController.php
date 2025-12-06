<?php
class AdminController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handleFileUpload(array $fileData): string|bool
    {
        $targetDir = "assets/images/products/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($fileData["name"]);
        $targetFile = $targetDir . time() . '_' . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (move_uploaded_file($fileData["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            return false;
        }
    }

    public function handleProductCrud(array $postData, array $fileData): string
    {
        if (!AuthManager::isAdmin()) {
            return "Error: Unauthorized access.";
        }

        $productModel = new Product($this->db);
        $action = $postData['action'] ?? '';
        $id = (int)($postData['productID'] ?? 0);
        $message = "Operation failed.";

        $validatedData = [
            'categoryID' => (int)($postData['categoryID'] ?? 0),
            'name' => trim(htmlspecialchars($postData['name'] ?? '')),
            'description' => trim(htmlspecialchars($postData['description'] ?? '')),
            'price' => (float)($postData['price'] ?? 0.00),
            'stockQuantity' => (int)($postData['stockQuantity'] ?? 0),
            'altText' => trim(htmlspecialchars($postData['altText'] ?? '')),
            'imagePath' => trim(htmlspecialchars($postData['currentImagePath'] ?? ''))
        ];

        if (empty($validatedData['name']) || empty($validatedData['altText']) || $validatedData['price'] <= 0) {
            return "Error: Required fields are missing or invalid.";
        }

        if (!empty($fileData['productPhoto']['name'])) {
            $newPath = $this->handleFileUpload($fileData['productPhoto']);
            if ($newPath) {
                $validatedData['imagePath'] = $newPath;
            } else {
                return "Error: File upload failed.";
            }
        }

        switch ($action) {
            case 'create':
                $productModel->categoryID = $validatedData['categoryID'];
                $productModel->name = $validatedData['name'];
                $productModel->description = $validatedData['description'];
                $productModel->price = $validatedData['price'];
                $productModel->stockQuantity = $validatedData['stockQuantity'];
                $productModel->imagePath = $validatedData['imagePath'];
                $productModel->altText = $validatedData['altText'];

                if ($productModel->create()) {
                    $message = "Product created successfully!";
                }
                break;

            case 'update':
                $productModel->productID = $id;
                if ($productModel->read($id)) {
                    $productModel->categoryID = $validatedData['categoryID'];
                    $productModel->name = $validatedData['name'];
                    $productModel->description = $validatedData['description'];
                    $productModel->price = $validatedData['price'];
                    $productModel->stockQuantity = $validatedData['stockQuantity'];
                    $productModel->imagePath = $validatedData['imagePath'];
                    $productModel->altText = $validatedData['altText'];

                    if ($productModel->update()) {
                        $message = "Product updated successfully!";
                    }
                } else {
                    $message = "Error: Product not found for update.";
                }
                break;

            case 'delete':
                if ($productModel->delete($id)) {
                    $message = "Product deleted successfully!";
                } else {
                    $message = "Error: Product could not be deleted.";
                }
                break;
        }

        return $message;
    }
}
