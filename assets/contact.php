<?php
// AJAX isteklerini kabul et
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Hata mesajlarını göster (development sırasında)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHPMailer sınıflarını yükle
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Sadece POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

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

try {
    $mail = new PHPMailer(true);

    // SMTP ayarları
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gmail SMTP sunucusu
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com'; // Gmail adresiniz
    $mail->Password = 'your-app-password'; // Gmail uygulama şifreniz
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // Gönderici ve alıcı
    $mail->setFrom('your-email@gmail.com', 'Website Contact Form');
    $mail->addAddress('ecemdiler@gmail.com', 'Ecem Diler');

    // İçerik
    $mail->isHTML(true);
    $mail->Subject = "İletişim Formu: " . $data['name'];
    $mail->Body = "
        <h3>Yeni İletişim Formu Mesajı</h3>
        <p><strong>Ad:</strong> {$data['name']}</p>
        <p><strong>E-posta:</strong> {$data['email']}</p>
        <p><strong>Mesaj:</strong><br>{$data['message']}</p>
    ";

    $mail->send();
    http_response_code(200);
    echo json_encode(["message" => "Mesajınız başarıyla gönderildi"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Gönderim hatası: " . $mail->ErrorInfo]);
}
?>