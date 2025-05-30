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
$adet = isset($_POST['adet']) ? $_POST['adet'] : 1;

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

// Önce ürünün sepette olup olmadığını kontrol et
$check_sql = "SELECT * FROM Sepet WHERE KullaniciID = ? AND UrunID = ?";
$check_params = array($user_id, $urun_id);
$check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
    // Ürün zaten sepette varsa adetini güncelle
    $update_sql = "UPDATE Sepet SET Adet = Adet + ? WHERE KullaniciID = ? AND UrunID = ?";
    $update_params = array($adet, $user_id, $urun_id);
    $update_stmt = sqlsrv_query($conn, $update_sql, $update_params);
    
    if ($update_stmt) {
        echo json_encode(['success' => true, 'message' => 'Ürün miktarı güncellendi!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ürün güncellenirken hata oluştu!']);
    }
} else {
    // Ürün sepette yoksa yeni ekle
    $insert_sql = "INSERT INTO Sepet (KullaniciID, UrunID, Adet) VALUES (?, ?, ?)";
    $insert_params = array($user_id, $urun_id, $adet);
    $insert_stmt = sqlsrv_query($conn, $insert_sql, $insert_params);
    
    if ($insert_stmt) {
        echo json_encode(['success' => true, 'message' => 'Ürün sepete eklendi!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ürün eklenirken hata oluştu!']);
    }
}
?> 