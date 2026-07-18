-- Run this once on your existing kaswa_db database (e.g. via phpMyAdmin's
-- SQL tab) to add the password_resets table that forgot-password.php and
-- reset-password.php need. This table was missing from the original
-- database/kaswa.sql, which is why you got:
--   "Base table or view not found: 1146 Table 'kaswa_db.password_resets'
--    doesn't exist"
--
-- Safe to run even if you're not sure whether it already exists --
-- CREATE TABLE IF NOT EXISTS will simply do nothing if it's already there.

USE kaswa_db;

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
