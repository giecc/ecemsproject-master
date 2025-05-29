<?php
// Hata raporlamayı en üst düzeye çıkar
error_reporting(E_ALL);
ini_set('display_errors', 0); // Ekranda hata göstermeyi kapat
ini_set('log_errors', 1); // Hata günlüğünü aç
ini_set('error_log', 'payment_errors.log'); // Hata günlüğü dosyası

session_start();

// JSON header'ı en başta ayarla
header('Content-Type: application/json');

try {
    // Gerekli dosyaları kontrol et
    if (!file_exists('modals/Urun.php')) {
        throw new Exception('Urun.php dosyası bulunamadı');
    }
    if (!file_exists('config/db.php')) {
        throw new Exception('db.php dosyası bulunamadı');
    }

    require_once 'modals/Urun.php';
    require_once 'config/db.php';

    // POST verilerini kontrol et
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Geçersiz istek metodu');
    }

    // POST verilerini logla
    error_log('POST verileri: ' . print_r($_POST, true));

    // Veritabanı bağlantısı
    $db = new Database();
    $conn = $db->getConnection();

    // POST verilerini al ve kontrol et
    if (!isset($_POST['cart']) || !isset($_POST['address']) || !isset($_POST['payment'])) {
        throw new Exception('Eksik veri gönderildi. Gerekli alanlar: cart, address, payment');
    }

    // JSON verilerini decode et
    $cart = json_decode($_POST['cart'], true);
    $address = json_decode($_POST['address'], true);
    $payment = json_decode($_POST['payment'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON verisi işlenirken hata oluştu: ' . json_last_error_msg());
    }

    // Verileri logla
    error_log('Cart: ' . print_r($cart, true));
    error_log('Address: ' . print_r($address, true));
    error_log('Payment: ' . print_r($payment, true));

    $conn->beginTransaction();

    // Sipariş numarası oluştur (YYYYMMDDXXX formatında)
    $siparisNo = date('Ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

    // Toplam tutarı işle
    $toplamTutar = $payment['total'];
    if (is_string($toplamTutar)) {
        // TL sembolünü ve boşlukları kaldır
        $toplamTutar = str_replace(['TL', ' '], '', $toplamTutar);
        // Virgülü noktaya çevir
        $toplamTutar = str_replace(',', '.', $toplamTutar);
        // Sadece sayısal değerleri al
        $toplamTutar = preg_replace('/[^0-9.]/', '', $toplamTutar);
    }
    $toplamTutar = (float) $toplamTutar;

    // Alıcı bilgilerini hazırla
    $aliciAdSoyad = $address['ad'] . ' ' . $address['soyad'];
    $aliciAdres = $address['adres'] . ', ' . $address['mahalle'] . ', ' . $address['ilce'] . '/' . $address['il'];

    // Sipariş ana tabloya ekle
    $stmt = $conn->prepare("INSERT INTO [Siparisler] ([SiparisNo], [KullaniciID], [ToplamTutar], [AliciAdSoyad], [AliciAdres], [Durum], [OdemeDurumu]) VALUES (?, ?, ?, ?, ?, 'Beklemede', 'Beklemede')");
    
    // Değerleri logla
    error_log("Siparisler değerleri: " . print_r([
        'SiparisNo' => $siparisNo,
        'KullaniciID' => (int)($_SESSION['user_id'] ?? 0),
        'ToplamTutar' => $toplamTutar,
        'AliciAdSoyad' => $aliciAdSoyad,
        'AliciAdres' => $aliciAdres
    ], true));

    try {
        $stmt->execute([
            $siparisNo,
            (int)($_SESSION['user_id'] ?? 0),
            $toplamTutar,
            $aliciAdSoyad,
            $aliciAdres
        ]);
    } catch (PDOException $e) {
        error_log("Siparisler tablosu INSERT hatası: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        error_log("Hata detayı: " . print_r($e->errorInfo, true));
        throw $e;
    }

    // Sipariş detaylarını ekle
    $stmt = $conn->prepare("INSERT INTO [SiparisDetaylari] ([SiparisNo], [UrunID], [UrunAdi], [UrunResim], [Adet], [BirimFiyat]) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($cart as $item) {
        error_log('SiparisDetaylari: ' . print_r($item, true));
        $urunID = (int) $item['id'];
        $urunAdi = (string) $item['name'];
        $adet = (int) $item['quantity'];
        
        // Fiyat değerini daha güvenli bir şekilde işle
        $fiyat = $item['price'];
        if (is_string($fiyat)) {
            // TL sembolünü ve boşlukları kaldır
            $fiyat = str_replace(['TL', ' '], '', $fiyat);
            // Virgülü noktaya çevir
            $fiyat = str_replace(',', '.', $fiyat);
            // Sadece sayısal değerleri al
            $fiyat = preg_replace('/[^0-9.]/', '', $fiyat);
        }
        $fiyat = (float) $fiyat;
        
        // Ürün resmi
        $urunResim = $item['image'] ?? '';
        
        // Değerleri logla
        error_log("SiparisDetaylari değerleri: " . print_r([
            'SiparisNo' => $siparisNo,
            'UrunID' => $urunID,
            'UrunAdi' => $urunAdi,
            'UrunResim' => $urunResim,
            'Adet' => $adet,
            'BirimFiyat' => $fiyat
        ], true));
        
        try {
            $stmt->execute([
                $siparisNo,
                $urunID,
                $urunAdi,
                $urunResim,
                $adet,
                $fiyat
            ]);
        } catch (PDOException $e) {
            error_log("SiparisDetaylari tablosu INSERT hatası: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            error_log("Hata detayı: " . print_r($e->errorInfo, true));
            throw $e;
        }
    }

    $conn->commit();
    
    // Başarılı yanıt döndür
    echo json_encode([
        'success' => true,
        'message' => 'Siparişiniz başarıyla alındı!',
        'order_id' => $siparisNo
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log('Ödeme işlemi hatası: ' . $e->getMessage());
    error_log('Hata detayı: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
}
?> 