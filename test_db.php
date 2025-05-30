<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// First, let's check if the SQL Server drivers are loaded
echo "<h2>Loaded PDO Drivers:</h2>";
print_r(PDO::getAvailableDrivers());

echo "<h2>Attempting Database Connection:</h2>";

$host = "DESKTOP-662B784\\SQLEXPRESS";
$dbname = "EWAHandmade";
$username = "Bitirme_Projesi";
$password = "12345";

try {
    // Try connection with different connection strings
    echo "Trying connection method 1:<br>";
    $conn1 = new PDO(
        "sqlsrv:Server=$host;Database=$dbname",
        $username,
        $password
    );
    echo "Connection 1 successful!<br>";
} catch (PDOException $e) {
    echo "Connection 1 failed: " . $e->getMessage() . "<br>";
}

try {
    echo "<br>Trying connection method 2:<br>";
    $conn2 = new PDO(
        "sqlsrv:Server=$host;Database=$dbname;Encrypt=0;TrustServerCertificate=1",
        $username,
        $password
    );
    echo "Connection 2 successful!<br>";
} catch (PDOException $e) {
    echo "Connection 2 failed: " . $e->getMessage() . "<br>";
}

// Check PHP extensions
echo "<h2>Loaded Extensions:</h2>";
print_r(get_loaded_extensions());
?> 