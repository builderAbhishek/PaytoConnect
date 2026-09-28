-- ====================================================================
-- SMART QR - Scan. Pay. Connect.
-- Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
-- Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
-- Production MySQL Database Schema & Seed Data
-- ====================================================================

-- 1. Create Database (If needed for XAMPP / Local MySQL)
CREATE DATABASE IF NOT EXISTS `smartqr` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smartqr`;

-- --------------------------------------------------------------------
-- Table: restaurants
-- Holds restaurant profiles and UPI details for dynamic QR routing
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `restaurants`;

CREATE TABLE `restaurants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `logo` VARCHAR(255) NULL,
  `address` TEXT NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `upi_id` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_restaurant_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table: admins
-- Store credentials for restaurant owners/managers
-- Each admin is strictly scoped to one restaurant_id
-- --------------------------------------------------------------------
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `restaurant_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_admin_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_admins_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table: customers
-- Captures customer info BEFORE UPI payment redirect
-- Scoped strictly to restaurant_id for multi-tenant isolation
-- --------------------------------------------------------------------
CREATE TABLE `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `restaurant_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `mobile` VARCHAR(15) NOT NULL,
  `customer_type` ENUM('Local', 'Outsider') NOT NULL DEFAULT 'Local',
  `bill_amount` DECIMAL(10, 2) NOT NULL,
  `marketing_consent` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_customer_restaurant` (`restaurant_id`),
  INDEX `idx_customer_mobile` (`mobile`),
  INDEX `idx_customer_created_at` (`created_at`),
  INDEX `idx_customer_type` (`customer_type`),
  CONSTRAINT `fk_customers_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Seed Data: Demo Restaurants
-- --------------------------------------------------------------------
INSERT INTO `restaurants` (`id`, `name`, `slug`, `logo`, `address`, `phone`, `upi_id`, `description`, `created_at`) VALUES
(1, 'Chaudhary Cafe & Family Restaurant', 'chaudhary', 'assets/images/chaudhary-logo.svg', 'Gadakul, Shohratgarh, Siddharthnagar', '9137470661', '9137470661@superyes', 'Fresh delicious fast food, meals, coffee, tea & snacks for families and highway travelers.', NOW()),
(2, 'Royal Cafe & Bakers', 'royal', 'assets/images/royal-logo.svg', 'Station Road, Naugarh, Siddharthnagar', '9876543210', 'royalcafe@okaxis', 'Finest bakery, cakes, pizzas and family dine-in in Naugarh.', NOW());

-- --------------------------------------------------------------------
-- Seed Data: Demo Admins
-- Password for both is: admin123
-- --------------------------------------------------------------------
INSERT INTO `admins` (`id`, `restaurant_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 1, 'Abhishek Chaudhary', 'chaudhary@smartqr.local', '$2y$10$XNFvGYducFGhyJqkn6eAwOFIS.ONUQRdprHI2/komfw6fBlUag3xS', NOW()),
(2, 2, 'Rohan Verma', 'royal@smartqr.local', '$2y$10$XNFvGYducFGhyJqkn6eAwOFIS.ONUQRdprHI2/komfw6fBlUag3xS', NOW());

-- --------------------------------------------------------------------
-- Seed Data: Sample Customers for Chaudhary Cafe (Restaurant ID = 1)
-- Provides instant realistic dashboard metrics for testing
-- --------------------------------------------------------------------
INSERT INTO `customers` (`restaurant_id`, `name`, `mobile`, `customer_type`, `bill_amount`, `marketing_consent`, `created_at`) VALUES
(1, 'Rahul Sharma', '9812345621', 'Local', 480.00, 1, DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(1, 'Aman Mishra', '9723456742', 'Outsider', 720.00, 1, DATE_SUB(NOW(), INTERVAL 55 MINUTE)),
(1, 'Priya Singh', '9634567890', 'Local', 350.00, 0, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'Vikram Gupta', '9545678901', 'Outsider', 1250.00, 1, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(1, 'Suresh Patel', '9456789012', 'Local', 220.00, 1, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(1, 'Neha Tiwari', '9367890123', 'Local', 640.00, 0, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(1, 'Deepak Verma', '9278901234', 'Outsider', 890.00, 1, DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(1, 'Anjali Dubey', '9189012345', 'Local', 530.00, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'Mohit Srivastava', '9090123456', 'Outsider', 1100.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'Sunil Yadav', '9891234567', 'Local', 410.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'Kavita Pandey', '9782345678', 'Local', 320.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 'Rajesh Jaiswal', '9673456789', 'Outsider', 1450.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(1, 'Alok Maurya', '9564567890', 'Local', 290.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 'Pooja Chaurasia', '9455678901', 'Local', 680.00, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(1, 'Vivek Tripathi', '9346789012', 'Outsider', 950.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY));

-- --------------------------------------------------------------------
-- Seed Data: Sample Customers for Royal Cafe (Restaurant ID = 2)
-- Proves multi-restaurant isolation in dashboard queries
-- --------------------------------------------------------------------
INSERT INTO `customers` (`restaurant_id`, `name`, `mobile`, `customer_type`, `bill_amount`, `marketing_consent`, `created_at`) VALUES
(2, 'Gaurav Sen', '9811122233', 'Local', 550.00, 1, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(2, 'Manish Saxena', '9822233344', 'Outsider', 1300.00, 0, DATE_SUB(NOW(), INTERVAL 3 HOUR));
