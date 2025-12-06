<?php
class Database
{
    private $host = 'localhost';
    private $dbName = 'DevTeam_VoltmartDB';
    private $user = 'root';
    private $password = 'root';

    private $pdo;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
        } catch (\PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Database connection failed.');
        }
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
