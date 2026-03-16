CREATE DATABASE IF NOT EXISTS `saln_project` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `saln_project`;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draft_token VARCHAR(64) NOT NULL UNIQUE,
    first_name VARCHAR(120) NOT NULL DEFAULT '',
    middle_name VARCHAR(120) NOT NULL DEFAULT '',
    last_name VARCHAR(120) NOT NULL DEFAULT '',
    position_title VARCHAR(255) NOT NULL DEFAULT '',
    agency VARCHAR(255) NOT NULL DEFAULT '',
    office_address VARCHAR(255) NOT NULL DEFAULT '',
    email VARCHAR(255) NOT NULL DEFAULT '',
    spouse_first_name VARCHAR(120) NOT NULL DEFAULT '',
    spouse_middle_name VARCHAR(120) NOT NULL DEFAULT '',
    spouse_last_name VARCHAR(120) NOT NULL DEFAULT '',
    spouse_position VARCHAR(255) NOT NULL DEFAULT '',
    spouse_agency VARCHAR(255) NOT NULL DEFAULT '',
    spouse_office_address VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS annex_forms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    annex_code CHAR(1) NOT NULL,
    source_page VARCHAR(30) NOT NULL DEFAULT '',
    filing_status VARCHAR(20) NOT NULL DEFAULT '',
    form_payload LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_annex_forms_user (user_id),
    KEY idx_annex_forms_code (annex_code),
    CONSTRAINT fk_annex_forms_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS annex_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    annex_code CHAR(1) NOT NULL,
    section_key VARCHAR(80) NOT NULL,
    row_uid VARCHAR(40) NOT NULL,
    row_data LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_section_row (user_id, section_key, row_uid),
    KEY idx_annex_entries_user_section (user_id, section_key),
    CONSTRAINT fk_annex_entries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
