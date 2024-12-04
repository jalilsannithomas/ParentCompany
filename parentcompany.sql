-- Create database
CREATE DATABASE IF NOT EXISTS ReThreadCollective
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ReThreadCollective;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(100) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    product_stock INT NOT NULL DEFAULT 0,
    product_desc TEXT,
    color ENUM('White', 'Black') DEFAULT 'White',
    size ENUM('Medium', 'Large') DEFAULT 'Medium',
    product_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) UNIQUE NOT NULL,
    user_pass VARCHAR(255) NOT NULL,
    user_phone VARCHAR(15) NOT NULL,
    user_role ENUM('customer', 'admin') DEFAULT 'customer',
    user_status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    p_id INT NOT NULL,
    user_id INT NOT NULL,
    qty INT NOT NULL,
    size ENUM('Medium', 'Large') DEFAULT 'Medium',
    color ENUM('White', 'Black') DEFAULT 'White',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (p_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('momo') DEFAULT 'momo',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Order Details Table
CREATE TABLE IF NOT EXISTS order_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    size ENUM('Medium', 'Large') DEFAULT 'Medium',
    color ENUM('White', 'Black') DEFAULT 'White',
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    reference_id VARCHAR(100) NOT NULL UNIQUE,
    payment_type ENUM('card', 'momo', 'cash') DEFAULT 'momo',
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Payment Logs Table
CREATE TABLE IF NOT EXISTS payment_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT,
    status_change VARCHAR(50),
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

-- Admin Logs Table
CREATE TABLE IF NOT EXISTS admin_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id)
);

-- Raffle Entries Table
CREATE TABLE IF NOT EXISTS raffle_entries (
    entry_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    instagram VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add indexes
ALTER TABLE products ADD INDEX idx_price (product_price);
ALTER TABLE products ADD INDEX idx_stock (product_stock);
ALTER TABLE orders ADD INDEX idx_status (order_status);
ALTER TABLE orders ADD INDEX idx_created (created_at);
ALTER TABLE users ADD INDEX idx_email (user_email);
ALTER TABLE users ADD INDEX idx_role (user_role);

-- Insert admin user (Password: Louise2000!)
INSERT INTO users (user_name, user_email, user_pass, user_phone, user_role) 
VALUES (
    'Admin', 
    'admin@parentcompany.com', 
    '$$2y$10$eakFP.M9NWD3xPmB3naS5OBsF51jBNRERNlS8mAk8Tf1nEx9A6lKy', -- This is 'Louise2000!!' hashed
    '+233000000000', 
    'admin'
);

-- Insert initial products
INSERT INTO products (product_name, product_price, product_desc, product_stock, color, product_image) VALUES
('Essential T Black', 35.00, 'Classic black t-shirt with logo', 50, 'Black', 'essential-black.jpg'),
('Essential T White', 35.00, 'Classic white t-shirt with logo', 50, 'White', 'essential-white.jpg');

--product_image

ALTER TABLE products ADD COLUMN product_image VARCHAR(255);
UPDATE products SET product_image = CONCAT(product_name, '.png');