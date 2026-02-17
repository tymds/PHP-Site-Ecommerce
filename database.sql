-- Création de la table User
CREATE TABLE IF NOT EXISTS `User` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- Stockage du hash bcrypt
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `balance` DECIMAL(10, 2) DEFAULT 0.00,
    `profile_picture` VARCHAR(255),
    `role` VARCHAR(20) DEFAULT 'user'
) ENGINE=InnoDB;

-- Création de la table Article
CREATE TABLE IF NOT EXISTS `Article` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `published_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `author_id` INT,
    `image_url` VARCHAR(255),
    FOREIGN KEY (`author_id`) REFERENCES `User`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Création de la table Cart (Panier)
CREATE TABLE IF NOT EXISTS `Cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `article_id` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `User`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`article_id`) REFERENCES `Article`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Création de la table Invoice (Facture)
CREATE TABLE IF NOT EXISTS `Invoice` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `amount` DECIMAL(10, 2) NOT NULL,
    `billing_address` VARCHAR(255) NOT NULL,
    `billing_city` VARCHAR(100) NOT NULL,
    `billing_zip_code` VARCHAR(20) NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `User`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Création de la table Stock
CREATE TABLE IF NOT EXISTS `Stock` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `article_id` INT NOT NULL UNIQUE,
    `quantity` INT DEFAULT 0,
    FOREIGN KEY (`article_id`) REFERENCES `Article`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;