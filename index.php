<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: assets/login-register.html");
    exit();
}

// Display welcome page
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ana Sayfa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .welcome {
            font-size: 24px;
            color: #333;
        }
        .logout {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout:hover {
            background-color: #c82333;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome">Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['user_name'] . ' ' . $_SESSION['user_surname']); ?></div>
            <a href="assets/logout.php" class="logout">Çıkış Yap</a>
        </div>
        
        <div class="user-info">
            <h3>Kullanıcı Bilgileri:</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <p><strong>Kullanıcı ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
        </div>
    </div>
</body>
</html> 