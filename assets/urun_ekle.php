<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        $urunAdi = $_POST['urun_adi'];
        $aciklama = $_POST['aciklama'];
        $kategori = $_POST['kategori'];
        $fiyat = $_POST['fiyat'];
        $resimURL = $_POST['resim_url'];
        $stok = $_POST['stok'];

        $sql = "INSERT INTO Urunler (UrunAdi, Aciklama, Kategori, Fiyat, ResimURL, Stok) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$urunAdi, $aciklama, $kategori, $fiyat, $resimURL, $stok,1]);

        echo "Ürün başarıyla eklendi!";
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ürün Ekle</title>
</head>
<body>
    <h1>Yeni Ürün Ekle</h1>
    <form method="POST">
        <div>
            <label>Ürün Adı:</label>
            <input type="text" name="urun_adi" required>
        </div>
        <div>
            <label>Açıklama:</label>
            <textarea name="aciklama" required></textarea>
        </div>
        <div>
            <label>Kategori:</label>
            <input type="text" name="kategori" required>
        </div>
        <div>
            <label>Fiyat:</label>
            <input type="number" step="0.01" name="fiyat" required>
        </div>
        <div>
            <label>Resim URL:</label>
            <input type="text" name="resim_url" required>
        </div>
        <div>
            <label>Stok:</label>
            <input type="number" name="stok" required>
        </div>
        <button type="submit">Ürün Ekle</button>
    </form>
</body>
</html>