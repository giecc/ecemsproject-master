<?php
session_start();
require_once 'messages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-register.html");
    exit();
}

if (isset($_GET['id'])) {
    $address_id = $_GET['id'];
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
        $_SESSION['error'] = "Veritabanı bağlantı hatası!";
        header("Location: accounts.php#addresses");
        exit();
    }

    // Önce adresin kullanıcıya ait olduğunu kontrol et
    $check_sql = "SELECT * FROM Adresler WHERE AdresID = ? AND KullaniciID = ?";
    $check_params = array($address_id, $user_id);
    $check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

    if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
        // Adresi sil
        $delete_sql = "DELETE FROM Adresler WHERE AdresID = ? AND KullaniciID = ?";
        $delete_params = array($address_id, $user_id);
        $delete_stmt = sqlsrv_query($conn, $delete_sql, $delete_params);

        if ($delete_stmt === false) {
            $_SESSION['error'] = "Adres silinirken bir hata oluştu!";
        } else {
            $_SESSION['success'] = "Adres başarıyla silindi!";
        }
    } else {
        $_SESSION['error'] = "Bu adresi silme yetkiniz yok!";
    }

    header("Location: accounts.php#addresses");
    exit();
} else {
    header("Location: accounts.php#addresses");
    exit();
}
?> 