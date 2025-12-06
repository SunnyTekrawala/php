<?php
// login.php
session_start();
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
    $formData['email'] = $validator->sanitizeEmail($_POST['email'] ?? '');
    $formData['password'] = $_POST['password'] ?? '';

    // Simple validation for required fields
    if (empty($formData['email'])) {
        $errors['email'] = "Email is required.";
    }
    if (empty($formData['password'])) {
        $errors['password'] = "Password is required.";
    }

    if (empty($errors)) {
        if ($authManager->loginUser($formData['email'], $formData['password'])) {
            $redirect = $_GET['redirect'] ?? 'index.php';
            header("Location: " . $redirect);
            exit;
        } else {
            $errors['general'] = "Login failed. Invalid email or password.";
        }
    }
}

// Function to get sticky value from POST/formData
function get_sticky($key)
{
    global $formData;
    return htmlspecialchars($formData[$key] ?? '');
}

$pageTitle = "Login";
include 'includes/header.php';
?>

<h2>User Login</h2>
<?php if (isset($errors['general'])): ?>
    <p style="color: red;"><?php echo $errors['general']; ?></p>
<?php elseif ($message): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo get_sticky('email'); ?>" required>
    <?php if (isset($errors['email'])): ?><span style="color: red;"><?php echo $errors['email']; ?></span><?php endif; ?><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <?php if (isset($errors['password'])): ?><span style="color: red;"><?php echo $errors['password']; ?></span><?php endif; ?><br>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a>.</p>

<?php include 'includes/footer.php'; ?>