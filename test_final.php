<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn = new PDO(
        "sqlsrv:Server=DESKTOP-662B784\\SQLEXPRESS;Database=EWAHandmade;TrustServerCertificate=1",
        "Bitirme_Projesi",
        "12345"
    );
    echo "Connection successful!<br>";
    
    // Test database access
    $result = $conn->query("SELECT @@VERSION as version");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<pre>SQL Server Version:\n" . $row['version'] . "</pre>";
    
    // Test database permissions
    $result = $conn->query("SELECT COUNT(*) as table_count FROM INFORMATION_SCHEMA.TABLES");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<pre>Number of tables in database: " . $row['table_count'] . "</pre>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Error Code: " . $e->getCode() . "<br>";
    if($e->errorInfo) {
        echo "Driver Error Info:<pre>";
        print_r($e->errorInfo);
        echo "</pre>";
    }
}
?> 