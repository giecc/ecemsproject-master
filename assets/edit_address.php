<?php
session_start();
require_once 'messages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-register.html");
    exit();
}

$serverName = "LAPTOP-069B9L8K\\SQLEXPRESS";
$connectionInfo = array(
    "Database" => "EWAHandmade",
    "UID" => "Bitirme_Projesi",
    "PWD" => "12345",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    $_SESSION['error'] = "Veritabanı bağlantı hatası!";
    header("Location: accounts.php#addresses");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address_id = $_POST['address_id'];
    $user_id = $_SESSION['user_id'];
    $address_title = $_POST['address_title'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $postal_code = $_POST['postal_code'];

    // Önce adresin kullanıcıya ait olduğunu kontrol et
    $check_sql = "SELECT * FROM Adresler WHERE AdresID = ? AND KullaniciID = ?";
    $check_params = array($address_id, $user_id);
    $check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

    if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
        // Adresi güncelle
        $update_sql = "UPDATE Adresler SET 
                      AdresBasligi = ?, 
                      Adres = ?, 
                      Sehir = ?, 
                      Ilce = ?, 
                      PostaKodu = ? 
                      WHERE AdresID = ? AND KullaniciID = ?";
        $update_params = array($address_title, $address, $city, $district, $postal_code, $address_id, $user_id);
        $update_stmt = sqlsrv_query($conn, $update_sql, $update_params);

        if ($update_stmt === false) {
            $_SESSION['error'] = "Adres güncellenirken bir hata oluştu!";
        } else {
            $_SESSION['success'] = "Adres başarıyla güncellendi!";
        }
    } else {
        $_SESSION['error'] = "Bu adresi düzenleme yetkiniz yok!";
    }

    header("Location: accounts.php#addresses");
    exit();
}

// GET isteği ile adres bilgilerini getir
if (isset($_GET['id'])) {
    $address_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM Adresler WHERE AdresID = ? AND KullaniciID = ?";
    $params = array($address_id, $user_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $_SESSION['error'] = "Adres bilgileri alınırken bir hata oluştu!";
        header("Location: accounts.php#addresses");
        exit();
    }

    $address = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if (!$address) {
        $_SESSION['error'] = "Adres bulunamadı!";
        header("Location: accounts.php#addresses");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <title>Adres Düzenle - EWA</title>
</head>
<body>
    <div class="container">
        <div class="edit-address-form">
            <h2>Adres Düzenle</h2>
            <form method="POST" action="edit_address.php">
                <input type="hidden" name="address_id" value="<?php echo $address['AdresID']; ?>">
                <div class="form-group">
                    <label>Adres Başlığı</label>
                    <input type="text" name="address_title" value="<?php echo htmlspecialchars($address['AdresBasligi']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Adres</label>
                    <textarea name="address" required><?php echo htmlspecialchars($address['Adres']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Şehir</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($address['Sehir']); ?>" required>
                </div>
                <div class="form-group">
                    <label>İlçe</label>
                    <input type="text" name="district" value="<?php echo htmlspecialchars($address['Ilce']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Posta Kodu</label>
                    <input type="text" name="postal_code" value="<?php echo htmlspecialchars($address['PostaKodu']); ?>" required>
                </div>
                <button type="submit" class="btn btn--primary">Adresi Güncelle</button>
                <a href="accounts.php#addresses" class="btn btn--secondary">İptal</a>
            </form>
        </div>
    </div>
</body>
</html> 