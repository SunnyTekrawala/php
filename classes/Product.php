<?php
class Product
{
    private Database $db;

    public ?int $productID = null;
    public int $categoryID = 0;
    public string $name = '';
    public string $description = '';
    public float $price = 0.0;
    public int $stockQuantity = 0;
    public string $imagePath = '';
    public string $altText = '';

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function create(): bool
    {
        $sql = "INSERT INTO Product (categoryID, name, description, price, stockQuantity, imagePath, altText)
                VALUES (:categoryID, :name, :description, :price, :stockQuantity, :imagePath, :altText)";
        $params = [
            'categoryID' => $this->categoryID,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stockQuantity' => $this->stockQuantity,
            'imagePath' => $this->imagePath,
            'altText' => $this->altText
        ];

        try {
            $this->db->query($sql, $params);
            $this->productID = (int)$this->db->lastInsertId();
            return true;
        } catch (\PDOException $e) {
            error_log('Product Create Error: ' . $e->getMessage());
            return false;
        }
    }

    public function read(int $id): ?self
    {
        $sql = "SELECT * FROM Product WHERE productID = :id LIMIT 1";
        $stmt = $this->db->query($sql, ['id' => $id]);
        $data = $stmt->fetch();
        if ($data) {
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
            return $this;
        }
        return null;
    }

    public function readAll(): array
    {
        $sql = "SELECT p.productID, p.name, p.description, p.price, p.stockQuantity, p.imagePath, p.altText, c.categoryID, c.categoryName
                FROM Product p
                LEFT JOIN Category c ON p.categoryID = c.categoryID
                ORDER BY p.productID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update(): bool
    {
        if (!$this->productID) return false;
        $sql = "UPDATE Product SET categoryID = :categoryID, name = :name, description = :description,
                price = :price, stockQuantity = :stockQuantity, imagePath = :imagePath, altText = :altText
                WHERE productID = :productID";
        $params = [
            'categoryID' => $this->categoryID,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stockQuantity' => $this->stockQuantity,
            'imagePath' => $this->imagePath,
            'altText' => $this->altText,
            'productID' => $this->productID
        ];
        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() >= 0;
        } catch (\PDOException $e) {
            error_log('Product Update Error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM Product WHERE productID = :id";
        try {
            $stmt = $this->db->query($sql, ['id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('Product Delete Error: ' . $e->getMessage());
            return false;
        }
    }
}
