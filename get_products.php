<?php
// Veritabanı bağlantı bilgileri
$serverName = "localhost"; // SQL Server adı
$connectionInfo = array(
    "Database" => "ecemsproject", // Veritabanı adı
    "UID" => "sa", // Kullanıcı adı
    "PWD" => "123456", // Şifre
    "CharacterSet" => "UTF-8"
);

// Veritabanı bağlantısını oluştur
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Kategori filtresi
$category = isset($_GET['category']) ? $_GET['category'] : null;

// SQL sorgusunu oluştur
$sql = "SELECT * FROM Urunler";
if ($category) {
    $sql .= " WHERE Kategori = ?";
}

$params = array();
if ($category) {
    $params[] = $category;
}

$stmt = sqlsrv_query($conn, $sql, $params);

$products = array();

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Sonuçları diziye ekle
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $products[] = array(
        'id' => $row['UrunID'],
        'name' => $row['UrunAdi'],
        'price' => $row['Fiyat'],
        'image' => $row['ResimYolu'],
        'category' => $row['Kategori'],
        'description' => $row['Aciklama']
    );
}

// JSON formatında döndür
header('Content-Type: application/json');
echo json_encode($products);

// Bağlantıyı kapat
sqlsrv_close($conn);
?> 