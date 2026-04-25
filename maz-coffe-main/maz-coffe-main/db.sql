-- db.sql
-- Create the database
CREATE DATABASE IF NOT EXISTS `coffee_shop`;
USE `coffee_shop`;

-- Create the products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: Insert sample data
INSERT INTO `products` (`name`, `image`, `category`, `description`, `price`) VALUES
('Espresso', 'espresso.jpg', 'boissons-chaudes', 'Un espresso classique et intense.', 2.50),
('Cappuccino', 'cappuccino.jpg', 'boissons-chaudes', 'Espresso avec lait moussé.', 3.50);
