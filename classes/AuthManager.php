<?php
class AuthManager
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function loginUser(string $email, string $password): bool
    {
        $userModel = new User($this->db);
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user->passwordHash)) {
            $_SESSION['user_id'] = $user->userID;
            $_SESSION['user_type'] = $user->userType;
            $_SESSION['user_name'] = $user->firstName;
            return true;
        }
        return false;
    }

    public function registerUser(array $data): bool
    {
        $userModel = new User($this->db);

        $userModel->firstName = $data['firstName'];
        $userModel->lastName = $data['lastName'];
        $userModel->email = $data['email'];
        $userModel->phone = $data['phone'];
        $userModel->userType = 'customer';

        $userModel->passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $addressData = [
            'street' => $data['street'],
            'city' => $data['city'],
            'provinceState' => $data['provinceState'],
            'country' => $data['country'],
            'postalCode' => $data['postalCode']
        ];

        return $userModel->create($addressData);
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return (self::isLoggedIn() && $_SESSION['user_type'] === 'admin');
    }
}
