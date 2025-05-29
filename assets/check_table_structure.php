<?php
require_once 'config/db.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Siparisler tablosu yapısı
    $query = "SELECT 
        COLUMN_NAME,
        DATA_TYPE,
        CHARACTER_MAXIMUM_LENGTH,
        IS_NULLABLE,
        COLUMN_DEFAULT
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'Siparisler'
    ORDER BY ORDINAL_POSITION";

    $stmt = $conn->query($query);
    echo "<h2>Siparisler Tablosu Yapısı:</h2>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

    // SiparisDetaylari tablosu yapısı
    $query = "SELECT 
        COLUMN_NAME,
        DATA_TYPE,
        CHARACTER_MAXIMUM_LENGTH,
        IS_NULLABLE,
        COLUMN_DEFAULT
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'SiparisDetaylari'
    ORDER BY ORDINAL_POSITION";

    $stmt = $conn->query($query);
    echo "<h2>SiparisDetaylari Tablosu Yapısı:</h2>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?> 