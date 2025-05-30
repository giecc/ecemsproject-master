<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $sql = "SELECT KullaniciID, Ad, Soyad, Email, SonGiris, Aktif FROM dbo.Kullanicilar ORDER BY KullaniciID";
    $stmt = $conn->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Output users as JSON
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Return error as JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?> 