<?php
require_once 'config/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Validate input
        $ad = trim($_POST['ad']);
        $soyad = trim($_POST['soyad']);
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $sifre = $_POST['sifre'];

        $errors = [];

        // Input validation
        if (empty($ad)) $errors[] = "Ad alanı zorunludur.";
        if (empty($soyad)) $errors[] = "Soyad alanı zorunludur.";
        if (!$email) $errors[] = "Geçerli bir email adresi giriniz.";
        if (strlen($sifre) < 6) $errors[] = "Şifre en az 6 karakter olmalıdır.";

        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT COUNT(*) FROM Kullanicilar WHERE Email = ?");
        $checkEmail->execute([$email]);
        if ($checkEmail->fetchColumn() > 0) {
            $errors[] = "Bu email adresi zaten kayıtlı.";
        }

        if (empty($errors)) {
            $sifre = password_hash($sifre, PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO Kullanicilar (Ad, Soyad, Email, Sifre) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$ad, $soyad, $email, $sifre]);

            $_SESSION['success_message'] = "Kayıt başarılı! Giriş yapabilirsiniz.";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $errors[] = "Sistem hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="auth-container">
        <h1>Kullanıcı Kayıt</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Ad:</label>
                <input type="text" name="ad" value="<?php echo isset($ad) ? htmlspecialchars($ad) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Soyad:</label>
                <input type="text" name="soyad" value="<?php echo isset($soyad) ? htmlspecialchars($soyad) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Şifre:</label>
                <input type="password" name="sifre" required>
                <small>En az 6 karakter olmalıdır.</small>
            </div>
            <button type="submit" class="auth-btn">Kayıt Ol</button>
        </form>
    </div>
</body>
</html>