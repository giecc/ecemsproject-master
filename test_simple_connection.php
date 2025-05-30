<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $connectionString = "sqlsrv:Server=DESKTOP-662B784\\SQLEXPRESS;Database=EWAHandmade;" .
                       "Encrypt=0;TrustServerCertificate=1;LoginTimeout=30;" .
                       "ConnectionPooling=0;MultipleActiveResultSets=1";
    
    $conn = new PDO(
        $connectionString,
        "Bitirme_Projesi",
        "Bitirme123!"
    );
    echo "Connection successful!";
    
    // Test a simple query
    $result = $conn->query("SELECT @@VERSION as version");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<pre>\nSQL Server Version:\n";
    print_r($row['version']);
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    echo "<br>Error Code: " . $e->getCode();
    
    // Print driver error information if available
    if($e->errorInfo) {
        echo "<br>Driver Error Info:<pre>";
        print_r($e->errorInfo);
        echo "</pre>";
    }
}
?> 