<?php
class Urun {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/db.php';
        $this->db = new Database();
    }
    
    public function getUrunById($id) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM urunler WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Ürün getirme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUrunFiyat($id) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT fiyat FROM urunler WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['fiyat'] : 0;
        } catch (Exception $e) {
            error_log("Ürün fiyat getirme hatası: " . $e->getMessage());
            return 0;
        }
    }
    
    public function stokGuncelle($id, $miktar) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("UPDATE urunler SET stok = stok - ? WHERE id = ? AND stok >= ?");
            return $stmt->execute([$miktar, $id, $miktar]);
        } catch (Exception $e) {
            error_log("Stok güncelleme hatası: " . $e->getMessage());
            return false;
        }
    }
}
?> 