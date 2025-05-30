<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $sql = "SELECT 
        COLUMN_NAME, 
        DATA_TYPE, 
        IS_NULLABLE 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'Kullanicilar'
    ORDER BY ORDINAL_POSITION";
    
    $stmt = $conn->query($sql);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 