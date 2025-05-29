<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor!']);
    exit();
}

if (!isset($_POST['favorite_id'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek!']);
    exit();
}

$favorite_id = $_POST['favorite_id'];
$user_id = $_SESSION['user_id'];

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

// Önce favorinin kullanıcıya ait olduğunu kontrol et
$check_sql = "SELECT * FROM Favoriler WHERE FavoriID = ? AND KullaniciID = ?";
$check_params = array($favorite_id, $user_id);
$check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
    // Favoriyi sil
    $delete_sql = "DELETE FROM Favoriler WHERE FavoriID = ? AND KullaniciID = ?";
    $delete_params = array($favorite_id, $user_id);
    $delete_stmt = sqlsrv_query($conn, $delete_sql, $delete_params);

    if ($delete_stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Favori silinirken bir hata oluştu!']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Ürün favorilerden çıkarıldı!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Bu favoriyi silme yetkiniz yok!']);
}
?> 