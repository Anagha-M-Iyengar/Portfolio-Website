-- ============================================================
--  setup_db.sql  —  anaghaz database
--  Run in phpMyAdmin → anaghaz → SQL tab
--  Safe to run multiple times (IF NOT EXISTS)
--  8 tables total
-- ============================================================

USE anaghaz;

-- TABLE 1: users
CREATE TABLE IF NOT EXISTS users (
    userid        INT(11)      NOT NULL AUTO_INCREMENT,
    Timestamp     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(255) NOT NULL,
    age           INT(3)       NOT NULL,
    username      VARCHAR(100) NOT NULL,
    mobile_number VARCHAR(15)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    PRIMARY KEY (userid),
    UNIQUE KEY email    (email),
    UNIQUE KEY username (username)
);

-- TABLE 2: contacts
CREATE TABLE IF NOT EXISTS contacts (
    contact_id   INT(11)      NOT NULL AUTO_INCREMENT,
    submitted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(255) NOT NULL,
    mobile       VARCHAR(15)  NOT NULL,
    message      TEXT         NOT NULL,
    company      VARCHAR(100) NOT NULL,
    PRIMARY KEY (contact_id)
);

-- TABLE 3: chat_messages
CREATE TABLE IF NOT EXISTS chat_messages (
    message_id INT(11)      NOT NULL AUTO_INCREMENT,
    sent_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    username   VARCHAR(100) NOT NULL,
    message    TEXT         NOT NULL,
    type       VARCHAR(150) NOT NULL DEFAULT 'user',
    PRIMARY KEY (message_id),
    INDEX idx_username (username),
    INDEX idx_type     (type)
);

-- TABLE 4: login_attempts
CREATE TABLE IF NOT EXISTS login_attempts (
    ip_address   VARCHAR(45) NOT NULL,
    attempts     INT(11)     NOT NULL DEFAULT 0,
    lock_until   INT(11)     NOT NULL DEFAULT 0,
    last_attempt TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ip_address)
);

-- TABLE 5: cipher_logs (Rail Fence)
CREATE TABLE IF NOT EXISTS cipher_logs (
    log_id       INT(11)      NOT NULL AUTO_INCREMENT,
    used_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    session_user VARCHAR(100) NOT NULL DEFAULT 'guest',
    ip_address   VARCHAR(45)  NOT NULL DEFAULT '',
    action       ENUM('encrypt','decrypt') NOT NULL,
    rails        INT(3)       NOT NULL,
    input_text   TEXT         NOT NULL,
    output_text  TEXT         NOT NULL,
    char_count   INT(11)      NOT NULL DEFAULT 0,
    source       ENUM('typed','txt_upload','pdf_upload') NOT NULL DEFAULT 'typed',
    PRIMARY KEY (log_id),
    INDEX idx_action (action),
    INDEX idx_user   (session_user)
);

-- TABLE 6: caesar_logs (Caesar Cipher)
CREATE TABLE IF NOT EXISTS caesar_logs (
    log_id       INT(11)      NOT NULL AUTO_INCREMENT,
    used_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    session_user VARCHAR(100) NOT NULL DEFAULT 'guest',
    ip_address   VARCHAR(45)  NOT NULL DEFAULT '',
    action       ENUM('encrypt','decrypt') NOT NULL,
    shift_key    INT(3)       NOT NULL,
    input_text   TEXT         NOT NULL,
    output_text  TEXT         NOT NULL,
    char_count   INT(11)      NOT NULL DEFAULT 0,
    source       ENUM('typed','txt_upload','pdf_upload') NOT NULL DEFAULT 'typed',
    PRIMARY KEY (log_id),
    INDEX idx_action (action),
    INDEX idx_user   (session_user)
);

-- TABLE 7: rsa_logs (RSA/AES OpenSSL)
-- NOTE: We store only METADATA — never the actual message or key content
CREATE TABLE IF NOT EXISTS rsa_logs (
    log_id          INT(11)      NOT NULL AUTO_INCREMENT,
    used_at         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    session_user    VARCHAR(100) NOT NULL DEFAULT 'guest',
    ip_address      VARCHAR(45)  NOT NULL DEFAULT '',
    action          ENUM('encrypt','decrypt') NOT NULL,
    output_filename VARCHAR(200) NOT NULL DEFAULT '',
    key_filename    VARCHAR(200) NOT NULL DEFAULT '',
    char_count      INT(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (log_id),
    INDEX idx_action (action),
    INDEX idx_user   (session_user),
    INDEX idx_date   (used_at)
);

-- ============================================================
-- TABLE 8: signature_logs  ← NEW
-- Logs every digital sign and verify operation.
--
-- Columns:
--   action        → 'sign' or 'verify'
--   file_hash     → SHA-256 hash of the file (hex string, 64 chars)
--   key_filename  → name of the key file used (not the key itself)
--   verify_result → 'valid' or 'invalid' (only set for verify actions)
-- ============================================================
CREATE TABLE IF NOT EXISTS signature_logs (
    log_id        INT(11)      NOT NULL AUTO_INCREMENT,
    used_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    session_user  VARCHAR(100) NOT NULL DEFAULT 'guest',
    ip_address    VARCHAR(45)  NOT NULL DEFAULT '',
    action        ENUM('sign','verify') NOT NULL,
    file_hash     VARCHAR(64)  NOT NULL DEFAULT '',
    key_filename  VARCHAR(200) NOT NULL DEFAULT '',
    verify_result ENUM('valid','invalid','') NOT NULL DEFAULT '',
    PRIMARY KEY (log_id),
    INDEX idx_action (action),
    INDEX idx_user   (session_user),
    INDEX idx_date   (used_at)
);

-- Done! You should now see 8 tables in phpMyAdmin under anaghaz.
