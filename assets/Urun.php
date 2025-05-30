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

    public function yeniUrunIDOlustur($kategori) {
        try {
            // Kategori önekini belirle
            $prefix = '';
            switch($kategori) {
                case 'Panço': $prefix = 'P'; break;
                case 'Elbise': $prefix = 'E'; break;
                case 'Sal': $prefix = 'S'; break;
                case 'Yöresel Dokuma': $prefix = 'YD'; break;
                case 'Fular': $prefix = 'F'; break;
                case 'Tunik': $prefix = 'T'; break;
                case 'Otantik Yelek': $prefix = 'OY'; break;
                case 'Bolero': $prefix = 'B'; break;
                case 'Pestemel': $prefix = 'PE'; break;
                default: $prefix = 'D';
            }
            
            // Bu kategorideki en son ID'yi bul
            $sql = "SELECT MAX(CAST(SUBSTRING(UrunID, LEN(?) + 1, LEN(UrunID)) AS INT)) as max_num 
                   FROM Urunler 
                   WHERE UrunID LIKE ? + '%'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$prefix, $prefix]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Yeni numarayı oluştur
            $nextNum = ($result['max_num'] ?? 0) + 1;
            return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            
        } catch(PDOException $e) {
            error_log("ID oluşturma hatası: " . $e->getMessage());
            return false;
        }
    }
}

// Test kodu (sadece geliştirme ortamında)
// $urun = new Urun();
// $urunler = $urun->tumUrunleriGetir();
// print_r($urunler);
?>