<?php
// Hata ayıklama modu (canlı ortamda 0 yapın)
define('DEBUG_MODE', 1);

// Temel ayarlar
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Hata yönetimi
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/php_errors.log');
}

/**
 * Güvenli JSON yanıtı oluşturur
 */
function sendJsonResponse($success, $message, $httpCode = 200) {
    http_response_code($httpCode);
    
    $response = [
        'success' => (bool)$success,
        'message' => (string)$message,
        'timestamp' => time()
    ];
    
    // JSON kodlama hatası kontrolü
    $json = @json_encode($response, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        // Acil durum yanıtı
        die('{"success":false,"message":"Sistem hatası","timestamp":'.time().'}');
    }
    
    die($json);
}

// Giriş verisini al
try {
    $input = file_get_contents('php://input');
    
    // POST verisini işle
    if (!empty($_POST)) {
        $data = $_POST;
    } elseif (!empty($input)) {
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendJsonResponse(false, "Geçersiz veri formatı", 400);
        }
    } else {
        sendJsonResponse(false, "Boş veri gönderildi", 400);
    }

    // Gerekli alan kontrolü
    $requiredFields = ['name', 'email', 'message', 'subject'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(false, "{$field} alanı gereklidir", 400);
        }
    }

    // Veri temizleme
    $cleanData = [
        'name' => htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8'),
        'email' => filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL),
        'message' => htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8'),
        'subject' => htmlspecialchars(trim($data['subject']), ENT_QUOTES, 'UTF-8'),
        'surname' => isset($data['surname']) ? htmlspecialchars(trim($data['surname']), ENT_QUOTES, 'UTF-8') : '',
        'phone' => isset($data['phone']) ? filter_var(trim($data['phone']), FILTER_SANITIZE_STRING) : ''
    ];

    // E-posta doğrulama
    if (!filter_var($cleanData['email'], FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(false, "Geçersiz e-posta adresi", 400);
    }

    // E-posta içeriği
    $emailContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f5f5f5; padding: 15px; text-align: center; }
                .content { padding: 20px; }
                .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Yeni İletişim Formu Mesajı</h2>
                </div>
                <div class='content'>
                    <p><strong>Ad:</strong> {$cleanData['name']}</p>
                    <p><strong>Soyad:</strong> {$cleanData['surname']}</p>
                    <p><strong>E-posta:</strong> <a href='mailto:{$cleanData['email']}'>{$cleanData['email']}</a></p>
                    <p><strong>Telefon:</strong> {$cleanData['phone']}</p>
                    <p><strong>Konu:</strong> {$cleanData['subject']}</p>
                    <p><strong>Mesaj:</strong></p>
                    <p>{$cleanData['message']}</p>
                </div>
                <div class='footer'>
                    <p>IP: {$_SERVER['REMOTE_ADDR']} | Tarih: " . date('d.m.Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>
    ";

    // E-posta başlıkları
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: EWA Handmade <noreply@ewahandmade.com>',
        'Reply-To: ' . $cleanData['name'] . ' <' . $cleanData['email'] . '>',
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 1 (Highest)'
    ];

    // E-posta gönderimi
    $mailSent = @mail(
        'ecemdiler@gmail.com',
        'EWA İletişim Formu: ' . $cleanData['subject'],
        $emailContent,
        implode("\r\n", $headers)
    );

    if (!$mailSent) {
        throw new Exception('E-posta gönderilemedi');
    }

    sendJsonResponse(true, "Mesajınız başarıyla gönderildi. Teşekkür ederiz!");

} catch (Exception $e) {
    error_log('Contact Form Error: ' . $e->getMessage());
    sendJsonResponse(false, "İşlem sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin.", 500);
}
?>