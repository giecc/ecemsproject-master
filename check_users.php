<?php
require_once 'assets/config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $database = new Database();
    
    // Query to get all users with their registration details
    $sql = "SELECT 
                KullaniciID,
                Ad,
                Soyad,
                Email,
                CONVERT(varchar, KayitTarihi, 120) as KayitTarihi,
                Aktif
            FROM Kullanicilar
            ORDER BY KayitTarihi DESC";
            
    $stmt = $database->prepare($sql);
    $database->execute($stmt);
    $users = $database->fetchAll($stmt);
    
    echo "<style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .container { padding: 20px; }
    </style>";
    
    echo "<div class='container'>";
    echo "<h2>Registered Users</h2>";
    
    if (!empty($users)) {
        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Email</th>
                <th>Kayıt Tarihi</th>
                <th>Durum</th>
              </tr>";
              
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['KullaniciID']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Ad']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Soyad']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['KayitTarihi']) . "</td>";
            echo "<td>" . ($user['Aktif'] ? 'Aktif' : 'Pasif') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No registered users found.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 