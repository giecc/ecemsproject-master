<?php
try {
    // Display PHP SQL Server extension info
    if (extension_loaded('sqlsrv')) {
        echo "SQL Server extension is loaded!\n";
        echo "SQL Server extension version: " . phpversion('sqlsrv') . "\n";
    } else {
        echo "SQL Server extension is NOT loaded!\n";
    }

    if (extension_loaded('pdo_sqlsrv')) {
        echo "PDO SQL Server extension is loaded!\n";
        echo "PDO SQL Server extension version: " . phpversion('pdo_sqlsrv') . "\n";
    } else {
        echo "PDO SQL Server extension is NOT loaded!\n";
    }

    // Try to connect
    require_once 'config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    // If we get here, connection was successful
    echo "Successfully connected to the database!\n";
    
    // Test query
    $stmt = $conn->query("SELECT @@VERSION as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "SQL Server Version: " . $result['version'];

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 