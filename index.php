<?php
// index.php
session_start();
require_once 'includes/autoloader.php';

$pageTitle = "Welcome to Voltmart";

// Includes the header and navbar
include 'includes/header.php';
?>

<h1>The Electric Marketplace: Voltmart ⚡</h1>
<p>Welcome to Voltmart, your premier destination for high-quality electronics and electric goods. We are committed to providing the latest technology at unbeatable prices, all while ensuring a seamless and secure shopping experience.</p>

<hr>

<h2>Featured Products</h2>
<div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
    <div style="width: 30%; border: 1px solid #ccc; padding: 10px; margin: 10px; text-align: center;">
        <h3>Electric Scooter</h3>
        <p>Efficient, fast, and eco-friendly personal transport.</p>
        <p>Price: $499.99</p>
        <button>View Details</button>
    </div>

    <div style="width: 30%; border: 1px solid #ccc; padding: 10px; margin: 10px; text-align: center;">
        <h3>Smart Watch</h3>
        <p>Track your fitness, manage calls, and stay connected.</p>
        <p>Price: $199.00</p>
        <button>View Details</button>
    </div>

    <div style="width: 30%; border: 1px solid #ccc; padding: 10px; margin: 10px; text-align: center;">
        <h3>Solar Panel Kit</h3>
        <p>Power your home with renewable energy.</p>
        <p>Price: $1,299.99</p>
        <button>View Details</button>
    </div>
</div>

<hr>

<h2>Our Mission</h2>
<p>At Voltmart, our mission is simple: to electrify your life. We believe in a sustainable future powered by cutting-edge technology. From smart home devices to electric vehicles, we are curating a selection that meets the demands of the modern, environmentally conscious consumer.</p>

<?php
// Includes the footer
include 'includes/footer.php';
?>