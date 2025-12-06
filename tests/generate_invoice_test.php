<?php
require_once __DIR__ . '/../includes/autoloader.php';

$pdfGen = new PDFGenerator();

$orderData = ['orderID' => 12345, 'total' => 100.00];
$userDetails = [
    'firstName' => 'Test',
    'lastName' => 'User',
    'street' => '123 Main St',
    'city' => 'Toronto',
    'provinceState' => 'ON',
    'postalCode' => 'A1A1A1',
    'country' => 'Canada'
];
$cartItems = [
    ['name' => 'Widget A', 'quantity' => 2, 'price' => 19.99],
    ['name' => 'Widget B', 'quantity' => 1, 'price' => 5.50]
];

$path = $pdfGen->generateInvoice($orderData, $userDetails, $cartItems);
if ($path) {
    echo "Invoice created: $path\n";
} else {
    echo "Failed to create invoice\n";
}
