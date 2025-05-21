<?php
require_once '../config/db.php';  // Nokta hatası düzeltildi (.. yerine ../)

class Urun {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Bağlantı hatası kontrolü
        if($this->db === false) {
            throw new Exception("Veritabanı bağlantısı başarısız");
        }
    }

    public function tumUrunleriGetir() {
        try {
            $sql = "SELECT * FROM Urunler WHERE Aktif = 1";
            $stmt = $this->db->query($sql);
            
            if(!$stmt) {
                throw new Exception("Sorgu çalıştırılamadı");
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Hata: " . $e->getMessage());
            return [];
        }
    }

    public function urunGetir($id) {
        try {
            $sql = "SELECT * FROM Urunler WHERE UrunID = ? AND Aktif = 1";
            $stmt = $this->db->prepare($sql);
            
            if(!$stmt->execute([$id])) {
                throw new Exception("Sorgu çalıştırılamadı");
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$result) {
                error_log("Ürün bulunamadı ID: $id");
                return false;
            }
            
            return $result;
        } catch(PDOException $e) {
            error_log("Hata: " . $e->getMessage());
            return false;
        }
    }
}

// Test kodu (sadece geliştirme ortamında)
// $urun = new Urun();
// $urunler = $urun->tumUrunleriGetir();
// print_r($urunler);
?>