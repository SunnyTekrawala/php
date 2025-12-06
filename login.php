<?php
// register.php
require_once 'includes/autoloader.php';

$db = new Database();
$validator = new InputValidator();
$authManager = new AuthManager($db);
$message = '';
$errors = [];
$formData = [];

if (AuthManager::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect form data
    $formData['firstName'] = $validator->sanitizeString($_POST['firstName'] ?? '');
    $formData['lastName'] = $validator->sanitizeString($_POST['lastName'] ?? '');
    $formData['email'] = $validator->sanitizeEmail($_POST['email'] ?? '');
    $formData['phone'] = $validator->sanitizeString($_POST['phone'] ?? '');
    $formData['street'] = $validator->sanitizeString($_POST['street'] ?? '');
    $formData['city'] = $validator->sanitizeString($_POST['city'] ?? '');
    $formData['provinceState'] = $validator->sanitizeString($_POST['provinceState'] ?? '');
    $formData['country'] = $validator->sanitizeString($_POST['country'] ?? '');
    $formData['postalCode'] = $validator->sanitizeString($_POST['postalCode'] ?? '');
    $formData['password'] = $_POST['password'] ?? '';
    $formData['confirm_password'] = $_POST['confirm_password'] ?? '';

    // Validate the data
    $errors = $validator->validateRegistration($formData);

    if (empty($errors)) {
        if ($authManager->registerUser($formData)) {
            // Attempt to log in the user immediately after successful registration
            if ($authManager->loginUser($formData['email'], $formData['password'])) {
                $redirect = $_GET['redirect'] ?? 'index.php';
                header("Location: " . $redirect);
                exit;
            } else {
                $message = "Registration successful, but automatic login failed. Please try logging in.";
            }
        } else {
            $errors['general'] = "Registration failed. Email might already be in use.";
        }
    }
}

// Function to get sticky value from POST/formData
function get_sticky($key)
{
    global $formData;
    return htmlspecialchars($formData[$key] ?? '');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Voltmart - Register</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <h2>New User Registration</h2>
    <?php if (isset($errors['general'])): ?>
        <p style="color: red;"><?php echo $errors['general']; ?></p>
    <?php elseif ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">

        <h3>Personal Details</h3>
        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" value="<?php echo get_sticky('firstName'); ?>" required>
        <?php if (isset($errors['firstName'])): ?><span style="color: red;"><?php echo $errors['firstName']; ?></span><?php endif; ?><br>

        <label for="lastName">Last Name:</label>
        <input type="text" name="lastName" value="<?php echo get_sticky('lastName'); ?>" required>
        <?php if (isset($errors['lastName'])): ?><span style="color: red;"><?php echo $errors['lastName']; ?></span><?php endif; ?><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo get_sticky('email'); ?>" required>
        <?php if (isset($errors['email'])): ?><span style="color: red;"><?php echo $errors['email']; ?></span><?php endif; ?><br>

        <label for="phone">Phone:</label>
        <input type="tel" name="phone" value="<?php echo get_sticky('phone'); ?>" required>
        <?php if (isset($errors['phone'])): ?><span style="color: red;"><?php echo $errors['phone']; ?></span><?php endif; ?><br>

        <h3>Address Details</h3>
        <label for="street">Street Address:</label>
        <input type="text" name="street" value="<?php echo get_sticky('street'); ?>" required>
        <?php if (isset($errors['street'])): ?><span style="color: red;"><?php echo $errors['street']; ?></span><?php endif; ?><br>

        <label for="city">City:</label>
        <input type="text" name="city" value="<?php echo get_sticky('city'); ?>" required>
        <?php if (isset($errors['city'])): ?><span style="color: red;"><?php echo $errors['city']; ?></span><?php endif; ?><br>

        <label for="provinceState">Province/State:</label>
        <input type="text" name="provinceState" value="<?php echo get_sticky('provinceState'); ?>" required>
        <?php if (isset($errors['provinceState'])): ?><span style="color: red;"><?php echo $errors['provinceState']; ?></span><?php endif; ?><br>

        <label for="country">Country:</label>
        <input type="text" name="country" value="<?php echo get_sticky('country'); ?>" required>
        <?php if (isset($errors['country'])): ?><span style="color: red;"><?php echo $errors['country']; ?></span><?php endif; ?><br>

        <label for="postalCode">Postal Code:</label>
        <input type="text" name="postalCode" value="<?php echo get_sticky('postalCode'); ?>" required>
        <?php if (isset($errors['postalCode'])): ?><span style="color: red;"><?php echo $errors['postalCode']; ?></span><?php endif; ?><br>

        <h3>Security</h3>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <?php if (isset($errors['password'])): ?><span style="color: red;"><?php echo $errors['password']; ?></span><?php endif; ?><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>
        <?php if (isset($errors['confirm_password'])): ?><span style="color: red;"><?php echo $errors['confirm_password']; ?></span><?php endif; ?><br>

        <button type="submit">Register</button>
    </form>

    <?php include 'includes/footer.php'; ?>
</body>

</html>