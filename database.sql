-- Siparişler tablosu
CREATE TABLE orders (
    order_id VARCHAR(11) PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    status ENUM('Beklemede', 'Onaylandı', 'Kargoda', 'Teslim Edildi', 'İptal Edildi') DEFAULT 'Beklemede',
    payment_status ENUM('Beklemede', 'Ödendi', 'İade Edildi') DEFAULT 'Beklemede',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Sipariş ürünleri tablosu
CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(11) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Sipariş numarası oluşturmak için fonksiyon
DELIMITER //
CREATE FUNCTION generate_order_number() 
RETURNS VARCHAR(11)
DETERMINISTIC
BEGIN
    DECLARE order_number VARCHAR(11);
    SET order_number = CONCAT(
        DATE_FORMAT(NOW(), '%y'), -- Yılın son 2 hanesi
        LPAD(MONTH(NOW()), 2, '0'), -- Ay (2 hane)
        LPAD(DAY(NOW()), 2, '0'), -- Gün (2 hane)
        LPAD(FLOOR(RAND() * 10000), 4, '0') -- Rastgele 4 hane
    );
    RETURN order_number;
END //
DELIMITER ; 