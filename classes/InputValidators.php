<?php
class InputValidator
{
    public function sanitizeString(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function sanitizeEmail(string $input): string
    {
        return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    }

    public function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['firstName'])) $errors['firstName'] = "First name is required.";
        if (empty($data['lastName'])) $errors['lastName'] = "Last name is required.";
        if (empty($data['phone'])) $errors['phone'] = "Phone number is required.";
        if (empty($data['street'])) $errors['street'] = "Street address is required.";
        if (empty($data['city'])) $errors['city'] = "City is required.";
        if (empty($data['postalCode'])) $errors['postalCode'] = "Postal code is required.";
        if (empty($data['provinceState'])) $errors['provinceState'] = "Province/State is required.";
        if (empty($data['country'])) $errors['country'] = "Country is required.";

        $email = $this->sanitizeEmail($data['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format.";
        }

        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if (strlen($password) < 8) {
            $errors['password'] = "Password must be at least 8 characters.";
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = "Passwords do not match.";
        }

        return $errors;
    }
}
