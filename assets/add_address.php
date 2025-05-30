<?php
session_start();
require_once 'messages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-register.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $telefon = $_POST['telefon'];
    $sehir = $_POST['il'];
    $ilce = $_POST['ilce'];
    $mahalle = $_POST['mahalle'];
    $adres = $_POST['address'];
    $adres_basligi = $_POST['address_title'];
    $fatura_turu = $_POST['fatura_turu'];

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

    $sql = "INSERT INTO Adresler (KullaniciID, Ad, Soyad, Telefon, Sehir, Ilce, Mahalle, Adres, AdresBasligi, FaturaTuru) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = array($user_id, $ad, $soyad, $telefon, $sehir, $ilce, $mahalle, $adres, $adres_basligi, $fatura_turu);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $_SESSION['error'] = "Adres eklenirken bir hata oluştu! " . print_r($errors, true);
    } else {
        $_SESSION['success'] = "Adres başarıyla eklendi!";
    }

    header("Location: accounts.php#addresses");
    exit();
}
?> 