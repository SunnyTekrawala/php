<?php
class CartManager
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addToCart(int $productId, int $quantity, array $productDetails): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'name' => $productDetails['name'],
                'price' => $productDetails['price'],
                'quantity' => $quantity
            ];
        }
        return true;
    }

    public function updateQuantity(int $productId, int $newQuantity): bool
    {
        if ($newQuantity <= 0) {
            return $this->removeFromCart($productId);
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
            return true;
        }
        return false;
    }

    public function removeFromCart(int $productId): bool
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    public function getCartItems(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public function getCartTotal(): float
    {
        $total = 0.00;
        foreach ($this->getCartItems() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clearCart(): void
    {
        $_SESSION['cart'] = [];
    }
}
