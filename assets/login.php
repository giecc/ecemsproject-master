<?php
// Hata raporlamayı aç
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

try {
    // POST verilerini al
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Eğer POST ile gelmediyse, raw input ile dene (JSON ise)
    if (!$email || !$password) {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
    }

    if (empty($email) || empty($password)) {
        throw new Exception('Email ve şifre gereklidir');
    }

    // SQL Server bağlantı bilgileri
    $serverName = "LAPTOP-069B9L8K\\SQLEXPRESS";
    $connectionInfo = array(
        "Database" => "EWAHandmade",
        "UID" => "Bitirme_Projesi",
        "PWD" => "12345",
        "CharacterSet" => "UTF-8"
    );

    // SQL Server bağlantısı
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn === false) {
        $errors = sqlsrv_errors();
        throw new Exception('Veritabanı bağlantı hatası: ' . $errors[0]['message']);
    }

    // Kullanıcıyı kontrol et
    $sql = "SELECT * FROM Kullanicilar WHERE Email = ?";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        throw new Exception('Sorgu hatası: ' . $errors[0]['message']);
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user && password_verify($password, $user['Sifre'])) {
        // Giriş başarılı
        $_SESSION['user_id'] = $user['KullaniciID'];
        $_SESSION['user_name'] = $user['Ad'];
        $_SESSION['user_surname'] = $user['Soyad'];
        $_SESSION['user_email'] = $user['Email'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Giriş başarılı',
            'redirect' => 'accounts.php'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Geçersiz email veya şifre'
        ]);
    }

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        sqlsrv_free_stmt($stmt);
    }
    if (isset($conn)) {
        sqlsrv_close($conn);
    }
}
?>