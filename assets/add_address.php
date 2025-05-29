<?php
session_start();
require_once 'messages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-register.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $address_title = $_POST['address_title'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $postal_code = $_POST['postal_code'];

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

    $sql = "INSERT INTO Adresler (KullaniciID, AdresBasligi, Adres, Sehir, Ilce, PostaKodu) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $params = array($user_id, $address_title, $address, $city, $district, $postal_code);
    
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $_SESSION['error'] = "Adres eklenirken bir hata oluştu!";
    } else {
        $_SESSION['success'] = "Adres başarıyla eklendi!";
    }

    header("Location: accounts.php#addresses");
    exit();
}
?> 