<?php
session_start();
require_once 'config/db.php';

// If already logged in, redirect to index
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: ../index.php");
    exit();
}

// Hata raporlamayı aç
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get form data
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $sifre = $_POST['sifre'];
        
        $errors = [];
        
        // Validate input
        if (!$email) {
            $errors[] = "Geçerli bir email adresi giriniz.";
        }
        if (empty($sifre)) {
            $errors[] = "Şifre alanı zorunludur.";
        }
        
        if (empty($errors)) {
            // Check user credentials
            $sql = "SELECT KullaniciID, Ad, Soyad, Email, Sifre FROM Kullanicilar WHERE Email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($sifre, $user['Sifre'])) {
                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['KullaniciID'];
                $_SESSION['user_name'] = $user['Ad'];
                $_SESSION['user_surname'] = $user['Soyad'];
                $_SESSION['user_email'] = $user['Email'];
                
                try {
                    // Update last login time
                    $updateSql = "UPDATE Kullanicilar SET SonGiris = GETDATE() WHERE KullaniciID = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->execute([$user['KullaniciID']]);
                } catch (Exception $e) {
                    // Log the error but don't prevent login
                    error_log("Failed to update SonGiris: " . $e->getMessage());
                }
                
                // Redirect to index page
                header("Location: ../index.php");
                exit();
            } else {
                $errors[] = "Geçersiz email veya şifre.";
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            header("Location: login-register.html");
            exit();
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_errors'] = ["Giriş sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin."];
        header("Location: login-register.html");
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header("Location: login-register.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error-messages {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .auth-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .auth-btn:hover {
            background-color: #0056b3;
        }
        .auth-links {
            text-align: center;
            margin-top: 15px;
        }
        .auth-links a {
            color: #007bff;
            text-decoration: none;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Giriş Yap</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php 
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Şifre:</label>
                <input type="password" name="sifre" required>
            </div>
            <button type="submit" class="auth-btn">Giriş Yap</button>
            <p class="auth-links">
                Hesabınız yok mu? <a href="kayit_ol.php">Kayıt Ol</a>
            </p>
        </form>
    </div>
</body>
</html>