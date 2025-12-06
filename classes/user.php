<?php
class User
{
    private Database $db;

    public ?int $userID = null;
    public string $userType = 'customer';
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $passwordHash = '';
    public string $phone = '';

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM User WHERE email = :email";
        $stmt = $this->db->query($sql, ['email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            $user = new User($this->db);
            foreach ($data as $key => $value) {
                if (property_exists($user, $key)) {
                    $user->{$key} = $value;
                }
            }
            return $user;
        }
        return null;
    }

    public function create(array $addressData): bool
    {
        try {
            $this->db->query("START TRANSACTION");

            $sqlUser = "INSERT INTO User (userType, firstName, lastName, email, passwordHash, phone) 
                        VALUES (:userType, :firstName, :lastName, :email, :passwordHash, :phone)";
            $paramsUser = [
                'userType' => $this->userType,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'passwordHash' => $this->passwordHash,
                'phone' => $this->phone
            ];
            $this->db->query($sqlUser, $paramsUser);
            $this->userID = (int) $this->db->lastInsertId();

            $sqlAddress = "INSERT INTO Address (userID, street, city, provinceState, country, postalCode)
                           VALUES (:userID, :street, :city, :provinceState, :country, :postalCode)";
            $paramsAddress = array_merge(['userID' => $this->userID], $addressData);
            $this->db->query($sqlAddress, $paramsAddress);

            $this->db->query("COMMIT");
            return true;
        } catch (\PDOException $e) {
            $this->db->query("ROLLBACK");
            error_log("User Registration Error: " . $e->getMessage());
            return false;
        }
    }

    public function read(int $id): ?array
    {
        $sql = "SELECT u.*, a.street, a.city, a.provinceState, a.country, a.postalCode
                FROM User u
                LEFT JOIN Address a ON u.userID = a.userID
                WHERE u.userID = :id LIMIT 1";
        $stmt = $this->db->query($sql, ['id' => $id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }
}
