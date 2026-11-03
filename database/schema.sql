CREATE DATABASE IF NOT EXISTS finance_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE finance_tracker;

CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(100) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    type        ENUM('income', 'expense') NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL,
    category_id       INT UNSIGNED NULL,
    amount            DECIMAL(10,2) NOT NULL,
    description       TEXT NULL,
    transaction_date  DATE NOT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transactions_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_transactions_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL,
    INDEX idx_transactions_user_date (user_id, transaction_date),
    INDEX idx_transactions_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (name, type)
SELECT * FROM (
    SELECT 'Salary'        AS name, 'income'  AS type UNION ALL
    SELECT 'Freelance',           'income'             UNION ALL
    SELECT 'Rent',                'expense'            UNION ALL
    SELECT 'Food',                'expense'            UNION ALL
    SELECT 'Transport',           'expense'            UNION ALL
    SELECT 'Entertainment',       'expense'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM categories LIMIT 1);
