<?php
session_start();

// Kullanıcı giriş yapmamışsa hata döndür
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Oturum açılmamış'
    ]);
    exit();
}

// Kullanıcı bilgilerini JSON olarak döndür
echo json_encode([
    'success' => true,
    'ad' => $_SESSION['user_name'],
    'soyad' => $_SESSION['user_surname'],
    'email' => $_SESSION['user_email'],
    'telefon' => $_SESSION['user_phone'] ?? ''
]);
?> 