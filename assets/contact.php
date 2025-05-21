<?php
// AJAX isteklerini kabul et
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Hata mesajlarını göster (development sırasında)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sadece POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

// Gerekli dosyaları include edin (varsa)
// require_once 'config.php';

// Form verilerini al
$data = json_decode(file_get_contents("php://input"), true);

// Verileri kontrol et
if (
    empty($data['name']) || 
    empty($data['email']) || 
    empty($data['message'])
) {
    http_response_code(400);
    echo json_encode(["message" => "Eksik bilgi girdiniz"]);
    exit;
}

// E-posta gönderimi
$to = "ecemdiler@gmail.com";
$subject = "İletişim Formu: " . $data['name'];
$message = "
    Ad: {$data['name']}
    E-posta: {$data['email']}
    Mesaj: {$data['message']}
";
$headers = "From: webmaster@ewahandmade.com\r\n";
$headers .= "Reply-To: {$data['email']}\r\n";

// E-posta gönder
if (mail($to, $subject, $message, $headers)) {
    http_response_code(200);
    echo json_encode(["message" => "Mesajınız gönderildi"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gönderim hatası"]);
}
?>