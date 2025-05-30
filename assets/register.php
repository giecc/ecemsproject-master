<?php
require 'Database.php';

// Hata raporlamayı maksimum seviyeye çıkar
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Giriş verisini logla
file_put_contents('register_debug.log', date('Y-m-d H:i:s')." - RAW INPUT: ".file_get_contents('php://input')."\n", FILE_APPEND);

$data = json_decode(file_get_contents('php://input'), true);

// JSON veri kontrolü
if (!$data) {
    file_put_contents('register_debug.log', "JSON DECODE FAILED\n", FILE_APPEND);
    die(json_encode(['success' => false, 'message' => 'Geçersiz JSON verisi']));
}

// Verileri logla
file_put_contents('register_debug.log', "Parsed Data: ".print_r($data, true)."\n", FILE_APPEND);

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Transaction başlat
    $conn->beginTransaction();
    
    // 1. ADIM: Email kontrolü
    $check = $conn->prepare("SELECT Email FROM Kullanicilar WITH (UPDLOCK) WHERE Email = ?");
    $check->execute([$data['email']]);
    $existing = $check->fetch();
    
    file_put_contents('register_debug.log', "Email check result: ".print_r($existing, true)."\n", FILE_APPEND);
    
    if ($existing) {
        $conn->rollBack();
        die(json_encode(['success' => false, 'message' => 'Bu email zaten kayıtlı']));
    }

    // 2. ADIM: Şifreyi hashle
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    file_put_contents('register_debug.log', "Password hash: $hashedPassword\n", FILE_APPEND);

    // 3. ADIM: Veri ekleme
    $sql = "INSERT INTO Kullanicilar (Ad, Soyad, Email, Sifre) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    file_put_contents('register_debug.log', "Prepared statement: $sql\n", FILE_APPEND);
    
    $params = [
        $data['name'],
        $data['surname'] ?? '',
        $data['email'],
        $hashedPassword
    ];
    
    file_put_contents('register_debug.log', "Params: ".print_r($params, true)."\n", FILE_APPEND);
    
    $insertResult = $stmt->execute($params);
    
    // 4. ADIM: Sonuç kontrolü
    $rowCount = $stmt->rowCount();
    $lastId = $conn->lastInsertId();
    
    file_put_contents('register_debug.log', "Insert result: ".var_export($insertResult, true)."\n", FILE_APPEND);
    file_put_contents('register_debug.log', "Row count: $rowCount\n", FILE_APPEND);
    file_put_contents('register_debug.log', "Last ID: $lastId\n", FILE_APPEND);
    
    if ($rowCount > 0) {
        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Kayıt başarılı',
            'user_id' => $lastId
        ]);
    } else {
        $conn->rollBack();
        $errorInfo = $stmt->errorInfo();
        file_put_contents('register_debug.log', "Error info: ".print_r($errorInfo, true)."\n", FILE_APPEND);
        
        die(json_encode([
            'success' => false,
            'message' => 'Kayıt eklenemedi',
            'error' => $errorInfo
        ]));
    }

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    file_put_contents('register_debug.log', "PDO Exception: ".$e->getMessage()."\n", FILE_APPEND);
    
    die(json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası',
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]));
}
?>