<?php
class Database {
    // Bağlantı bilgileri
    private $host = "LAPTOP-069B9L8K\SQLEXPRESS01"; // SQL Server instance
    private $dbname = "EWAHandmade"; // Veritabanı adı
    private $username = "Bitirme_Projesi"; // SQL Server Authentication kullanıcı adı
    private $password = "12345"; // SQL Server Authentication şifresi
    
    // PDO bağlantı nesnesi
    private $conn;
    
    // Hata ayıklama modu
    private $debugMode = true;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            // Bağlantıyı oluştur
            $connectionString = "sqlsrv:Server=$this->host;Database=$this->dbname;" .
                              "Encrypt=0;TrustServerCertificate=1;LoginTimeout=30;" .
                              "ConnectionPooling=0;MultipleActiveResultSets=1";
            
            $this->conn = new PDO(
                $connectionString,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Test connection immediately
            $this->conn->query("SELECT 1");
            
            // Bağlantı başarılı mesajı (debug modda)
            if ($this->debugMode) {
                error_log("[" . date('Y-m-d H:i:s') . "] Veritabanına başarıyla bağlanıldı");
            }
            
        } catch (PDOException $e) {
            // Hata mesajını logla
            $errorMsg = "[" . date('Y-m-d H:i:s') . "] Bağlantı hatası: " . $e->getMessage();
            error_log($errorMsg);
            
            // Kullanıcı dostu hata mesajı
            if ($this->debugMode) {
                die("Veritabanı bağlantı hatası: " . $e->getMessage() . 
                    "<br>Error Code: " . $e->getCode() .
                    "<br>Driver Error Info: <pre>" . print_r($e->errorInfo, true) . "</pre>");
            } else {
                die("Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.");
            }
        }
    }
    
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    public function execute($stmt, $params = []) {
        return $stmt->execute($params);
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function fetch($stmt) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetchAll($stmt) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    public function getConnection() {
        // Bağlantıyı kontrol et ve gerekirse yeniden bağlan
        if (!$this->conn || !$this->testConnection()) {
            $this->connect();
        }
        return $this->conn;
    }
    
    private function testConnection() {
        try {
            $this->conn->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function closeConnection() {
        $this->conn = null;
    }
    
    public function __destruct() {
        $this->closeConnection();
    }
}
?>