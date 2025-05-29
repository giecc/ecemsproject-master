<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor!']);
    exit();
}

if (!isset($_POST['urun_id'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$urun_id = $_POST['urun_id'];

$serverName = "LAPTOP-069B9L8K\\SQLEXPRESS";
$connectionInfo = array(
    "Database" => "EWAHandmade",
    "UID" => "Bitirme_Projesi",
    "PWD" => "12345",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    echo json_encode(['success' => false, 'message' => 'Veritabanı bağlantı hatası!']);
    exit();
}

// Aynı ürün favorilerde var mı kontrol et
$check_sql = "SELECT * FROM Favoriler WHERE KullaniciID = ? AND UrunID = ?";
$check_params = array($user_id, $urun_id);
$check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
    echo json_encode(['success' => false, 'message' => 'Bu ürün zaten favorilerde!']);
    exit();
}

// Favoriye ekle
$sql = "INSERT INTO Favoriler (KullaniciID, UrunID) VALUES (?, ?)";
$params = array($user_id, $urun_id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Favori eklenirken hata oluştu!']);
} else {
    echo json_encode(['success' => true, 'message' => 'Ürün favorilere eklendi!']);
}
?> 