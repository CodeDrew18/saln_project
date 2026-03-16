<?php

declare(strict_types=1);

use Dotenv\Dotenv;

if (!function_exists('saln_base_dir')) {
    function saln_base_dir(): string
    {
        return dirname(__DIR__);
    }
}

if (!function_exists('saln_load_env_once')) {
    function saln_load_env_once(): void
    {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $baseDir = saln_base_dir();
        $envPath = $baseDir . DIRECTORY_SEPARATOR . '.env';

        if (class_exists(Dotenv::class) && file_exists($envPath)) {
            try {
                Dotenv::createImmutable($baseDir)->safeLoad();
            } catch (Throwable $exception) {
                error_log('SALN dotenv load warning: ' . $exception->getMessage());
            }
        }

        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trimmed = trim((string) $line);
                    if ($trimmed === '' || strpos($trimmed, '#') === 0) {
                        continue;
                    }

                    $parts = explode('=', $trimmed, 2);
                    if (count($parts) !== 2) {
                        continue;
                    }

                    $key = trim((string) $parts[0]);
                    $value = trim((string) $parts[1]);
                    $value = trim($value, "\"'");

                    if ($key !== '' && !isset($_ENV[$key])) {
                        $_ENV[$key] = $value;
                    }
                }
            }
        }

        $loaded = true;
    }
}

if (!function_exists('saln_env')) {
    function saln_env(string $key, string $default = ''): string
    {
        saln_load_env_once();

        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }

        $normalized = trim((string) $value);
        $normalized = trim($normalized, "\"'");

        return $normalized === '' ? $default : $normalized;
    }
}

if (!function_exists('saln_db_connection')) {
    function saln_db_connection(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $dbHost = saln_env('DB_HOST', '127.0.0.1');
        $dbPort = saln_env('DB_PORT', '3306');
        $dbUser = saln_env('DB_USER', 'root');
        $dbPass = saln_env('DB_PASSWORD', '');

        $dbNameRaw = saln_env('DB_NAME', saln_env('DB_DATABASE', 'saln_project'));
        $dbName = preg_replace('/[^A-Za-z0-9_]/', '', $dbNameRaw);
        if ($dbName === null || $dbName === '') {
            $dbName = 'saln_project';
        }

        $serverDsn = 'mysql:host=' . $dbHost . ';port=' . $dbPort . ';charset=utf8mb4';
        $serverPdo = new PDO($serverDsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $serverPdo->exec('CREATE DATABASE IF NOT EXISTS `' . $dbName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $dbDsn = 'mysql:host=' . $dbHost . ';port=' . $dbPort . ';dbname=' . $dbName . ';charset=utf8mb4';
        $pdo = new PDO($dbDsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        saln_db_ensure_schema($pdo);

        return $pdo;
    }
}

if (!function_exists('saln_db_ensure_schema')) {
    function saln_db_ensure_schema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                draft_token VARCHAR(64) NOT NULL UNIQUE,
                first_name VARCHAR(120) NOT NULL DEFAULT "",
                middle_name VARCHAR(120) NOT NULL DEFAULT "",
                last_name VARCHAR(120) NOT NULL DEFAULT "",
                position_title VARCHAR(255) NOT NULL DEFAULT "",
                agency VARCHAR(255) NOT NULL DEFAULT "",
                office_address VARCHAR(255) NOT NULL DEFAULT "",
                email VARCHAR(255) NOT NULL DEFAULT "",
                spouse_first_name VARCHAR(120) NOT NULL DEFAULT "",
                spouse_middle_name VARCHAR(120) NOT NULL DEFAULT "",
                spouse_last_name VARCHAR(120) NOT NULL DEFAULT "",
                spouse_position VARCHAR(255) NOT NULL DEFAULT "",
                spouse_agency VARCHAR(255) NOT NULL DEFAULT "",
                spouse_office_address VARCHAR(255) NOT NULL DEFAULT "",
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS annex_forms (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                annex_code CHAR(1) NOT NULL,
                source_page VARCHAR(30) NOT NULL DEFAULT "",
                filing_status VARCHAR(20) NOT NULL DEFAULT "",
                form_payload LONGTEXT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_annex_forms_user (user_id),
                KEY idx_annex_forms_code (annex_code),
                CONSTRAINT fk_annex_forms_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS annex_entries (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}

if (!function_exists('saln_draft_token')) {
    function saln_draft_token(string $preferredToken = ''): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $preferredToken = preg_replace('/[^A-Za-z0-9_\-]/', '', trim($preferredToken));
        if (is_string($preferredToken) && $preferredToken !== '') {
            $_SESSION['saln_draft_token'] = substr($preferredToken, 0, 64);
            return $_SESSION['saln_draft_token'];
        }

        $existing = $_SESSION['saln_draft_token'] ?? '';
        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        try {
            $token = bin2hex(random_bytes(16));
        } catch (Throwable $exception) {
            $token = uniqid('draft_', true);
        }

        $_SESSION['saln_draft_token'] = $token;

        return $token;
    }
}

if (!function_exists('saln_get_or_create_user_id')) {
    function saln_get_or_create_user_id(array $defaults = [], string $preferredDraftToken = ''): int
    {
        $pdo = saln_db_connection();
        $draftToken = saln_draft_token($preferredDraftToken);

        $select = $pdo->prepare('SELECT id FROM users WHERE draft_token = :draft_token LIMIT 1');
        $select->execute(['draft_token' => $draftToken]);
        $existing = $select->fetch();

        if (is_array($existing) && isset($existing['id'])) {
            $userId = (int) $existing['id'];
            if (!empty($defaults)) {
                saln_db_upsert_user_profile($userId, $defaults);
            }

            return $userId;
        }

        $insert = $pdo->prepare(
            'INSERT INTO users (draft_token, first_name, middle_name, last_name, email) VALUES (:draft_token, :first_name, :middle_name, :last_name, :email)'
        );
        $insert->execute([
            'draft_token' => $draftToken,
            'first_name' => (string) ($defaults['first_name'] ?? ''),
            'middle_name' => (string) ($defaults['middle_name'] ?? ''),
            'last_name' => (string) ($defaults['last_name'] ?? ''),
            'email' => (string) ($defaults['email'] ?? ''),
        ]);

        return (int) $pdo->lastInsertId();
    }
}

if (!function_exists('saln_db_upsert_user_profile')) {
    function saln_db_upsert_user_profile(int $userId, array $profile): void
    {
        if ($userId <= 0 || $profile === []) {
            return;
        }

        $allowed = [
            'first_name',
            'middle_name',
            'last_name',
            'position_title',
            'agency',
            'office_address',
            'email',
            'spouse_first_name',
            'spouse_middle_name',
            'spouse_last_name',
            'spouse_position',
            'spouse_agency',
            'spouse_office_address',
        ];

        $setParts = [];
        $params = ['user_id' => $userId];

        foreach ($allowed as $column) {
            if (!array_key_exists($column, $profile)) {
                continue;
            }

            $value = $profile[$column];
            if (!is_scalar($value) && $value !== null) {
                continue;
            }

            $setParts[] = $column . ' = :' . $column;
            $params[$column] = trim((string) ($value ?? ''));
        }

        if ($setParts === []) {
            return;
        }

        $sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :user_id';
        $stmt = saln_db_connection()->prepare($sql);
        $stmt->execute($params);
    }
}

if (!function_exists('saln_db_save_annex_form')) {
    function saln_db_save_annex_form(int $userId, string $annexCode, string $sourcePage, array $payload, string $fillingStatus = ''): int
    {
        $annexCode = strtoupper(substr(trim($annexCode), 0, 1));
        if (!in_array($annexCode, ['A', 'B', 'C'], true)) {
            $annexCode = 'A';
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($jsonPayload === false) {
            $jsonPayload = '{}';
        }

        $stmt = saln_db_connection()->prepare(
            'INSERT INTO annex_forms (user_id, annex_code, source_page, filing_status, form_payload) VALUES (:user_id, :annex_code, :source_page, :filing_status, :form_payload)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'annex_code' => $annexCode,
            'source_page' => substr(trim($sourcePage), 0, 30),
            'filing_status' => substr(trim($fillingStatus), 0, 20),
            'form_payload' => $jsonPayload,
        ]);

        return (int) saln_db_connection()->lastInsertId();
    }
}

if (!function_exists('saln_db_rows_from_post')) {
    function saln_db_rows_from_post(array $postData, array $fields): array
    {
        $normalized = [];
        $maxCount = 0;

        foreach ($fields as $field) {
            $value = $postData[$field] ?? [];
            $items = is_array($value) ? $value : [$value];
            $normalized[$field] = $items;
            if (count($items) > $maxCount) {
                $maxCount = count($items);
            }
        }

        $rows = [];

        for ($index = 0; $index < $maxCount; $index++) {
            $row = [];
            $hasData = false;

            foreach ($fields as $field) {
                $rawValue = $normalized[$field][$index] ?? '';
                $value = is_scalar($rawValue) ? trim((string) $rawValue) : '';
                $row[$field] = substr($value, 0, 5000);
                if ($value !== '') {
                    $hasData = true;
                }
            }

            if ($hasData) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}

if (!function_exists('saln_db_section_annex_code')) {
    function saln_db_section_annex_code(string $sectionKey): string
    {
        if (strpos($sectionKey, 'annexA') === 0) {
            return 'A';
        }

        if (strpos($sectionKey, 'annexB') === 0) {
            return 'B';
        }

        if (strpos($sectionKey, 'annexC') === 0) {
            return 'C';
        }

        return 'A';
    }
}

if (!function_exists('saln_db_replace_section_rows')) {
    function saln_db_replace_section_rows(int $userId, string $annexCode, string $sectionKey, array $rows): void
    {
        saln_db_clear_rows($userId, $sectionKey);

        foreach ($rows as $row) {
            if (is_array($row)) {
                saln_db_insert_row($userId, $annexCode, $sectionKey, $row);
            }
        }
    }
}

if (!function_exists('saln_db_list_rows')) {
    function saln_db_list_rows(int $userId, string $sectionKey, array $allowedFields): array
    {
        $stmt = saln_db_connection()->prepare(
            'SELECT row_uid, row_data FROM annex_entries WHERE user_id = :user_id AND section_key = :section_key ORDER BY id ASC'
        );
        $stmt->execute([
            'user_id' => $userId,
            'section_key' => $sectionKey,
        ]);

        $rows = [];

        while ($dbRow = $stmt->fetch()) {
            $decoded = json_decode((string) ($dbRow['row_data'] ?? '{}'), true);
            if (!is_array($decoded)) {
                $decoded = [];
            }

            $row = ['id' => (string) ($dbRow['row_uid'] ?? '')];
            foreach ($allowedFields as $field) {
                $value = $decoded[$field] ?? '';
                $row[$field] = is_scalar($value) ? (string) $value : '';
            }

            $rows[] = $row;
        }

        return $rows;
    }
}

if (!function_exists('saln_db_insert_row')) {
    function saln_db_insert_row(int $userId, string $annexCode, string $sectionKey, array $rowData): array
    {
        try {
            $rowUid = bin2hex(random_bytes(8));
        } catch (Throwable $exception) {
            $rowUid = uniqid('row_', true);
        }

        $payload = [];
        foreach ($rowData as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $payload[$key] = is_scalar($value) ? substr(trim((string) $value), 0, 5000) : '';
        }

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{}';
        }

        $stmt = saln_db_connection()->prepare(
            'INSERT INTO annex_entries (user_id, annex_code, section_key, row_uid, row_data) VALUES (:user_id, :annex_code, :section_key, :row_uid, :row_data)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'annex_code' => strtoupper(substr($annexCode, 0, 1)),
            'section_key' => $sectionKey,
            'row_uid' => $rowUid,
            'row_data' => $json,
        ]);

        $payload['id'] = $rowUid;

        return $payload;
    }
}

if (!function_exists('saln_db_update_row')) {
    function saln_db_update_row(int $userId, string $annexCode, string $sectionKey, string $rowUid, array $rowData): bool
    {
        $payload = [];
        foreach ($rowData as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $payload[$key] = is_scalar($value) ? substr(trim((string) $value), 0, 5000) : '';
        }

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{}';
        }

        $stmt = saln_db_connection()->prepare(
            'UPDATE annex_entries
             SET annex_code = :annex_code, row_data = :row_data
             WHERE user_id = :user_id AND section_key = :section_key AND row_uid = :row_uid'
        );

        $stmt->execute([
            'annex_code' => strtoupper(substr($annexCode, 0, 1)),
            'row_data' => $json,
            'user_id' => $userId,
            'section_key' => $sectionKey,
            'row_uid' => $rowUid,
        ]);

        return $stmt->rowCount() > 0;
    }
}

if (!function_exists('saln_db_delete_row')) {
    function saln_db_delete_row(int $userId, string $sectionKey, string $rowUid): bool
    {
        $stmt = saln_db_connection()->prepare(
            'DELETE FROM annex_entries WHERE user_id = :user_id AND section_key = :section_key AND row_uid = :row_uid'
        );
        $stmt->execute([
            'user_id' => $userId,
            'section_key' => $sectionKey,
            'row_uid' => $rowUid,
        ]);

        return $stmt->rowCount() > 0;
    }
}

if (!function_exists('saln_db_clear_rows')) {
    function saln_db_clear_rows(int $userId, string $sectionKey): void
    {
        $stmt = saln_db_connection()->prepare(
            'DELETE FROM annex_entries WHERE user_id = :user_id AND section_key = :section_key'
        );
        $stmt->execute([
            'user_id' => $userId,
            'section_key' => $sectionKey,
        ]);
    }
}
