<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login-register.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get form data
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        
        $errors = [];
        
        // Validate input
        if (empty($name)) $errors[] = "Ad alanı zorunludur.";
        if (empty($surname)) $errors[] = "Soyad alanı zorunludur.";
        if (!$email) $errors[] = "Geçerli bir email adresi giriniz.";
        
        // Check if email is already used by another user
        if ($email !== $_SESSION['user_email']) {
            $checkEmail = $conn->prepare("SELECT COUNT(*) FROM Kullanicilar WHERE Email = ? AND KullaniciID != ?");
            $checkEmail->execute([$email, $_SESSION['user_id']]);
            if ($checkEmail->fetchColumn() > 0) {
                $errors[] = "Bu email adresi başka bir kullanıcı tarafından kullanılıyor.";
            }
        }
        
        if (empty($errors)) {
            // Update user information
            $sql = "UPDATE Kullanicilar SET Ad = ?, Soyad = ?, Email = ? WHERE KullaniciID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $surname, $email, $_SESSION['user_id']]);
            
            // Update session variables
            $_SESSION['user_name'] = $name;
            $_SESSION['user_surname'] = $surname;
            $_SESSION['user_email'] = $email;
            
            $_SESSION['success_message'] = "Profil bilgileriniz başarıyla güncellendi.";
        } else {
            $_SESSION['error_message'] = implode(", ", $errors);
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Bir hata oluştu: " . $e->getMessage();
    }
    
    // Redirect back to accounts page
    header("Location: accounts.php");
    exit();
}
?> 