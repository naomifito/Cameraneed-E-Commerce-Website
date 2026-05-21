-- SnapRent Multimedia Database Schema
-- Created: December 2023

-- Drop existing tables if they exist (for clean setup)
DROP TABLE IF EXISTS payment;
DROP TABLE IF EXISTS order_item;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customer;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS category;

-- Create database (uncomment if you want to create a new database)
-- CREATE DATABASE snaprent;
-- USE snaprent;

-- Table: category
CREATE TABLE category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: product
CREATE TABLE product (
    product_id VARCHAR(50) PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL,
    image_url TEXT,
    specs TEXT,
    stock INT NOT NULL DEFAULT 0,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(category_id)
);

-- Table: customer
CREATE TABLE customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    ktp_id VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: orders
CREATE TABLE orders (
    order_id VARCHAR(50) PRIMARY KEY,
    customer_id INT NOT NULL,
    rental_date DATE NOT NULL,
    notes TEXT,
    status VARCHAR(50) NOT NULL DEFAULT 'MENUNGGU_PEMBAYARAN',
    total_amount DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
);

-- Table: order_item
CREATE TABLE order_item (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    duration INT NOT NULL DEFAULT 1,
    price_per_day DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- Table: payment
CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    payment_status VARCHAR(50) NOT NULL DEFAULT 'MENUNGGU',
    payment_date TIMESTAMP,
    proof_of_payment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Insert sample data into category table
INSERT INTO category (name, description) VALUES
('Kamera Mirrorless', 'Kamera tanpa cermin dengan hasil foto profesional'),
('Kamera DSLR', 'Kamera digital dengan cermin refleks dan lensa yang dapat diganti'),
('Handycam', 'Kamera video genggam untuk merekam video dengan mudah'),
('Lighting Softbox', 'Peralatan pencahayaan dengan difuser untuk hasil foto lebih lembut'),
('Lighting RGB', 'Pencahayaan dengan warna yang dapat diatur untuk efek kreatif'),
('Tripod', 'Penyangga kamera untuk hasil foto dan video yang stabil'),
('Microphone Wireless', 'Mikrofon tanpa kabel untuk kebebasan bergerak'),
('Microphone Shotgun', 'Mikrofon directional dengan fokus audio ke arah depan'),
('Audio Recorder', 'Perekam audio berkualitas tinggi'),
('Projector', 'Proyektor untuk presentasi dan pemutaran video');

-- Insert sample data into product table
INSERT INTO product (product_id, category_id, name, price_per_day, image_url, specs, stock, is_available) VALUES
('kamera-mirrorless-1', 1, 'Sony A6400 Kit 16-50mm', 350000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Sony+A6400', '24.2MP APS-C, 4K Video, flip screen, cocok untuk konten kreator.', 3, TRUE),
('kamera-mirrorless-2', 1, 'Fujifilm X-T30 + 18-55mm', 380000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Fuji+X-T30', '26.1MP, film simulation, cocok untuk foto dan video sinematik.', 2, TRUE),
('kamera-dslr-1', 2, 'Canon EOS 80D Kit', 320000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Canon+EOS+80D', '24.2MP, Dual Pixel AF, cocok untuk event dan produksi sederhana.', 2, TRUE),
('kamera-dslr-2', 2, 'Nikon D750 Body', 400000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Nikon+D750', 'Full frame 24.3MP, low light bagus, cocok untuk foto wedding.', 1, TRUE),
('handycam-1', 3, 'Sony Handycam HD', 250000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Sony+Handycam', 'Full HD recording, stabilizer, cocok untuk dokumentasi acara.', 3, TRUE),
('handycam-2', 3, 'Panasonic 4K Handycam', 300000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Panasonic+4K', '4K video, zoom panjang, cocok untuk seminar dan live event.', 2, TRUE),
('lighting-softbox-1', 4, 'Softbox 2 Head 85W', 150000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Softbox+2Head', 'Paket 2 lampu dengan softbox, cocok untuk studio kecil.', 5, TRUE),
('lighting-softbox-2', 4, 'Softbox 3 Head 135W', 200000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Softbox+3Head', 'Output lebih terang, cocok untuk video produk.', 3, TRUE),
('lighting-rgb-1', 5, 'RGB Tube Light 60cm', 180000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=RGB+Tube', 'RGB penuh, efek lighting kreatif untuk konten.', 4, TRUE),
('lighting-rgb-2', 5, 'RGB Panel Light', 200000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=RGB+Panel', 'Panel RGB dengan dimmer, cocok untuk studio.', 2, TRUE),
('tripod-1', 6, 'Tripod Aluminium 1.6m', 50000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Tripod+Aluminium', 'Ringan dan kokoh, cocok untuk kamera mirrorless/DSLR.', 10, TRUE),
('tripod-2', 6, 'Tripod Video Fluid Head', 90000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Tripod+Video', 'Kepala fluid, gerakan kamera halus untuk video.', 5, TRUE),
('mic-wireless-1', 7, 'Wireless Mic 2 Transmitter', 170000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Wireless+Mic+2TX', 'Mic clip-on, cocok untuk interview dan vlog.', 3, TRUE),
('mic-wireless-2', 7, 'Wireless Mic Compact', 150000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Mic+Compact', 'Unit kecil, mudah dipasang pada kamera/smartphone.', 4, TRUE),
('mic-shotgun-1', 8, 'Shotgun Mic On-Camera', 90000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Shotgun+Mic', 'Fokus suara ke depan, cocok untuk run-and-gun.', 5, TRUE),
('mic-shotgun-2', 8, 'Shotgun Mic Boom', 130000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Shotgun+Boom', 'Dengan boom pole, cocok untuk produksi film.', 3, TRUE),
('audio-recorder-1', 9, 'Zoom H4n Pro', 160000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Zoom+H4n', 'Perekam audio portable, 4 track, cocok untuk interview.', 2, TRUE),
('audio-recorder-2', 9, 'Tascam DR-40X', 150000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Tascam+DR40X', 'Perekam dengan XLR, cocok untuk produksi film.', 2, TRUE),
('projector-1', 10, 'Projector HD 3000 lumens', 250000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Projector+HD', 'Cocok untuk presentasi kantor dan kelas.', 3, TRUE),
('projector-2', 10, 'Projector Full HD 4000 lumens', 320000, 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Projector+FHD', 'Lebih terang, cocok untuk event dan pemutaran film.', 2, TRUE);

-- Insert sample customers
INSERT INTO customer (full_name, whatsapp, ktp_id) VALUES
('Budi Santoso', '081234567890', 'ID-12345678'),
('Siti Rahayu', '081987654321', 'ID-87654321'),
('Ahmad Hidayat', '087812345678', 'ID-23456789');

-- Insert sample orders
INSERT INTO orders (order_id, customer_id, rental_date, notes, status, total_amount) VALUES
('ORD-2309-ABC123', 1, '2023-12-20', 'Untuk acara seminar', 'MENUNGGU_PEMBAYARAN', 550000),
('ORD-2309-DEF456', 2, '2023-12-25', 'Shooting prewedding', 'LUNAS', 1200000),
('ORD-2309-GHI789', 3, '2023-12-28', 'Kebutuhan dokumentasi acara keluarga', 'LUNAS', 400000);

-- Insert sample order items
INSERT INTO order_item (order_id, product_id, quantity, duration, price_per_day, subtotal) VALUES
('ORD-2309-ABC123', 'kamera-dslr-1', 1, 1, 320000, 320000),
('ORD-2309-ABC123', 'tripod-1', 1, 1, 50000, 50000),
('ORD-2309-ABC123', 'mic-wireless-1', 1, 1, 170000, 170000),
('ORD-2309-DEF456', 'kamera-mirrorless-1', 1, 2, 350000, 700000),
('ORD-2309-DEF456', 'lighting-softbox-2', 1, 2, 200000, 400000),
('ORD-2309-DEF456', 'tripod-2', 1, 1, 90000, 90000),
('ORD-2309-GHI789', 'handycam-1', 1, 1, 250000, 250000),
('ORD-2309-GHI789', 'mic-shotgun-1', 1, 1, 90000, 90000),
('ORD-2309-GHI789', 'tripod-1', 1, 1, 50000, 50000);

-- Insert sample payments
INSERT INTO payment (order_id, amount, payment_method, transaction_id, payment_status, payment_date, proof_of_payment) VALUES
('ORD-2309-ABC123', 550000, 'TRANSFER', NULL, 'MENUNGGU', NULL, NULL),
('ORD-2309-DEF456', 1200000, 'TRANSFER', 'TRX123456', 'DIKONFIRMASI', '2023-12-23 15:30:00', 'bukti_pembayaran_def456.jpg'),
('ORD-2309-GHI789', 400000, 'TRANSFER', 'TRX789012', 'DIKONFIRMASI', '2023-12-26 10:15:00', 'bukti_pembayaran_ghi789.jpg');

-- Create Views for Common Queries

-- View for available products
CREATE VIEW vw_available_products AS
SELECT p.product_id, c.name AS category_name, p.name, p.price_per_day, p.specs, p.stock
FROM product p
JOIN category c ON p.category_id = c.category_id
WHERE p.is_available = TRUE AND p.stock > 0;

-- View for current orders
CREATE VIEW vw_current_orders AS
SELECT o.order_id, c.full_name, c.whatsapp, o.rental_date, o.status, o.total_amount,
       o.created_at, o.updated_at
FROM orders o
JOIN customer c ON o.customer_id = c.customer_id
WHERE o.rental_date >= CURRENT_DATE
ORDER BY o.rental_date ASC;

-- View for order details
CREATE VIEW vw_order_details AS
SELECT oi.order_item_id, o.order_id, p.name AS product_name, c.name AS category_name,
       oi.quantity, oi.duration, oi.price_per_day, oi.subtotal,
       o.status, o.rental_date, cu.full_name, cu.whatsapp
FROM order_item oi
JOIN orders o ON oi.order_id = o.order_id
JOIN product p ON oi.product_id = p.product_id
JOIN category c ON p.category_id = c.category_id
JOIN customer cu ON o.customer_id = cu.customer_id;

-- Create Stored Procedures

-- Procedure to create new order
DELIMITER //
CREATE PROCEDURE sp_create_order(
    IN p_full_name VARCHAR(255),
    IN p_whatsapp VARCHAR(20),
    IN p_ktp_id VARCHAR(50),
    IN p_rental_date DATE,
    IN p_notes TEXT,
    OUT p_order_id VARCHAR(50)
)
BEGIN
    DECLARE v_customer_id INT;
    
    -- Generate order ID
    SET p_order_id = CONCAT('ORD-', DATE_FORMAT(NOW(), '%y%m'), '-', SUBSTRING(MD5(RAND()), 1, 6));
    
    -- Check if customer exists, if not create new
    SELECT customer_id INTO v_customer_id FROM customer 
    WHERE whatsapp = p_whatsapp LIMIT 1;
    
    IF v_customer_id IS NULL THEN
        INSERT INTO customer(full_name, whatsapp, ktp_id)
        VALUES(p_full_name, p_whatsapp, p_ktp_id);
        
        SET v_customer_id = LAST_INSERT_ID();
    END IF;
    
    -- Create order with 0 amount (will be updated when items are added)
    INSERT INTO orders(order_id, customer_id, rental_date, notes, status, total_amount)
    VALUES(p_order_id, v_customer_id, p_rental_date, p_notes, 'MENUNGGU_PEMBAYARAN', 0);
    
END //
DELIMITER ;

-- Procedure to add item to order
DELIMITER //
CREATE PROCEDURE sp_add_order_item(
    IN p_order_id VARCHAR(50),
    IN p_product_id VARCHAR(50),
    IN p_quantity INT,
    IN p_duration INT
)
BEGIN
    DECLARE v_price_per_day DECIMAL(10, 2);
    DECLARE v_subtotal DECIMAL(12, 2);
    DECLARE v_current_stock INT;
    
    -- Get product price and check availability
    SELECT price_per_day, stock INTO v_price_per_day, v_current_stock
    FROM product WHERE product_id = p_product_id;
    
    IF v_current_stock >= p_quantity THEN
        -- Calculate subtotal
        SET v_subtotal = v_price_per_day * p_quantity * p_duration;
        
        -- Insert order item
        INSERT INTO order_item(order_id, product_id, quantity, duration, price_per_day, subtotal)
        VALUES(p_order_id, p_product_id, p_quantity, p_duration, v_price_per_day, v_subtotal);
        
        -- Update order total
        UPDATE orders
        SET total_amount = total_amount + v_subtotal
        WHERE order_id = p_order_id;
        
        -- Reserve stock immediately when item is added
        UPDATE product
        SET stock = stock - p_quantity
        WHERE product_id = p_product_id;
    ELSE
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Not enough stock available for this product';
    END IF;
    
END //
DELIMITER ;

-- Procedure to update order status
DELIMITER //
CREATE PROCEDURE sp_update_order_status(
    IN p_order_id VARCHAR(50),
    IN p_new_status VARCHAR(50)
)
BEGIN
    UPDATE orders
    SET status = p_new_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE order_id = p_order_id;
END //
DELIMITER ;

-- Procedure to record payment
DELIMITER //
CREATE PROCEDURE sp_record_payment(
    IN p_order_id VARCHAR(50),
    IN p_amount DECIMAL(12, 2),
    IN p_payment_method VARCHAR(50),
    IN p_transaction_id VARCHAR(100),
    IN p_proof_of_payment TEXT
)
BEGIN
    INSERT INTO payment(order_id, amount, payment_method, transaction_id, payment_status, payment_date, proof_of_payment)
    VALUES(p_order_id, p_amount, p_payment_method, p_transaction_id, 'MENUNGGU', CURRENT_TIMESTAMP, p_proof_of_payment);
END //
DELIMITER ;

-- Create triggers

-- Trigger to update product stock when order status changes to SIAP_DIAMBIL
DELIMITER //
CREATE TRIGGER trg_update_stock_after_order_confirmation
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    -- Return stock if order is cancelled
    IF NEW.status = 'DIBATALKAN' AND OLD.status != 'DIBATALKAN' THEN
        UPDATE product p
        JOIN order_item oi ON p.product_id = oi.product_id
        SET p.stock = p.stock + oi.quantity
        WHERE oi.order_id = NEW.order_id;
    END IF;
END //
DELIMITER ;

-- Trigger to auto-update order status when payment is confirmed
DELIMITER //
CREATE TRIGGER trg_update_order_status_after_payment_confirmation
AFTER UPDATE ON payment
FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'DIKONFIRMASI' AND OLD.payment_status != 'DIKONFIRMASI' THEN
        UPDATE orders
        SET status = 'PEMBAYARAN_DIKONFIRMASI'
        WHERE order_id = NEW.order_id AND status = 'MENUNGGU_PEMBAYARAN';
    END IF;
END //
DELIMITER ;
