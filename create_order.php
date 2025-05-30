<?php
require_once 'config.php';
session_start();

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']);
    exit();
}

// POST ile gelen verileri al
$data = json_decode(file_get_contents('php://input'), true);

// Gerekli alanlar
$kullaniciID = $_SESSION['user_id'];
$aliciAdSoyad = isset($data['aliciAdSoyad']) ? $data['aliciAdSoyad'] : '';
$aliciAdres = isset($data['aliciAdres']) ? $data['aliciAdres'] : '';
$sepet = isset($data['sepet']) ? $data['sepet'] : [];

default_timezone_set('Europe/Istanbul');
$siparisTarihi = date('Y-m-d H:i:s');

if (empty($aliciAdSoyad) || empty($aliciAdres) || empty($sepet)) {
    echo json_encode(['success' => false, 'message' => 'Tüm alanlar zorunludur.']);
    exit();
}

// Sipariş numarası oluştur (11 haneli: YılAyGün + 5 rastgele rakam)
$siparisNo = date('ymd') . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

// Toplam tutarı hesapla
$toplamTutar = 0;
foreach ($sepet as $urun) {
    $toplamTutar += $urun['adet'] * $urun['birimFiyat'];
}

try {
    $conn->beginTransaction();
    // Sipariş kaydı
    $stmt = $conn->prepare("INSERT INTO Siparisler (SiparisNo, KullaniciID, SiparisTarihi, ToplamTutar, AliciAdSoyad, AliciAdres, Durum, OdemeDurumu) VALUES (?, ?, ?, ?, ?, ?, 'Beklemede', 'Beklemede')");
    $stmt->execute([$siparisNo, $kullaniciID, $siparisTarihi, $toplamTutar, $aliciAdSoyad, $aliciAdres]);

    // Sipariş detayları
    $stmtDetay = $conn->prepare("INSERT INTO SiparisDetaylari (SiparisNo, UrunID, UrunAdi, UrunResim, Adet, BirimFiyat) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($sepet as $urun) {
        $stmtDetay->execute([
            $siparisNo,
            $urun['urunID'],
            $urun['urunAdi'],
            $urun['urunResim'],
            $urun['adet'],
            $urun['birimFiyat']
        ]);
    }
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Siparişiniz başarıyla oluşturuldu.', 'siparisNo' => $siparisNo]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Sipariş oluşturulamadı.']);
}
?> 