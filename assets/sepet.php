<?php
session_start();
require_once 'Urun.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['urun_id'])) {
    $urun = new Urun();
    $urunDetay = $urun->urunGetir($_POST['urun_id']);
    
    if ($urunDetay) {
        // Sepet yoksa oluştur
        if (!isset($_SESSION['sepet'])) {
            $_SESSION['sepet'] = [];
        }
        
        // Ürün sepette varsa miktarını artır, yoksa ekle
        if (isset($_SESSION['sepet'][$urunDetay['UrunID']])) {
            $_SESSION['sepet'][$urunDetay['UrunID']]['miktar'] += 1;
        } else {
            $_SESSION['sepet'][$urunDetay['UrunID']] = [
                'id' => $urunDetay['UrunID'],
                'ad' => $urunDetay['UrunAdi'],
                'fiyat' => $urunDetay['Fiyat'],
                'miktar' => 1,
                'resim' => $urunDetay['ResimURL']
            ];
        }
        
        // Başarılı yanıt dön (AJAX için)
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'sepet_count' => count($_SESSION['sepet'])
            ]);
            exit;
        }
        
        header('Location: urunler.php?success=1');
        exit;
    }
}

// Hata durumunda
header('Location: urunler.php?error=urun_bulunamadi');
exit;
?>