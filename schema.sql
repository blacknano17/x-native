-- ==============================================================================
-- X-NATIVE STARTER SCHEMA (MySQL)
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'superadmin', 'Akses penuh seluruh modul sistem'),
(2, 'manager', 'Akses operasional dan approval'),
(3, 'staff', 'Akses entri data operasional');

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'staff',
  `company` VARCHAR(100) DEFAULT 'PT Sinar Maju Bersama',
  `avatar` VARCHAR(255) NULL,
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun default: password = password123 (hashed bcrypt)
INSERT IGNORE INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `company`, `avatar`, `status`) VALUES
(1, 'Danu Superadmin', 'admin', 'admin@sinarmaju.co.id', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'superadmin', 'PT Sinar Maju Bersama', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150', 'active'),
(2, 'Budi Logistik Manager', 'manager', 'budi@sinarmaju.co.id', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'manager', 'PT Sinar Maju Bersama', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150', 'active');

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `username` VARCHAR(50) NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
