<?php
class Urun {
    private $conn;
    private $table_name = "Urunler";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUrun($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE UrunID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?> 