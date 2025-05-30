<?php
class Database {
    private $host = "localhost";
    private $db_name = "your_database_name";
    private $username = "your_username";
    private $password = "your_password";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "sqlsrv:Server=" . $this->host . ";Database=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
            throw $e;
        }

        return $this->conn;
    }
}
?> 