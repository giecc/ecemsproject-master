<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "Connection successful!";
    
    // Try a simple query
    $sql = "SELECT @@VERSION as version";
    $result = $database->query($sql);
    $row = $database->fetch_array($result);
    echo "<br>SQL Server Version: " . $row['version'];
    
    // Test database existence
    $sql = "SELECT DB_NAME() as current_db";
    $result = $database->query($sql);
    $row = $database->fetch_array($result);
    echo "<br>Current Database: " . $row['current_db'];
    
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
} 