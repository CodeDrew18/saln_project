<?php
require 'vendor/autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'db.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpWord\TemplateProcessor;
use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Helper to load additional env file manually (optional)
$loadEnvFile = static function (string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return;

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '#') === 0) continue;
        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) continue;

        $key = trim($parts[0]);
        $value = trim($parts[1], "\"'");
        if ($key !== '' && !isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
};

$loadEnvFile(__DIR__ . '/.env');

// Helper to safely get env values
$getEnv = static function (string $key, string $default = ''): string {
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    $normalized = trim((string) $value);
    $normalized = trim($normalized, "\"'");

    return $normalized === '' ? $default : $normalized;
};

// Helper to ensure array from form input
$toArray = static function ($value): array {
    if (is_array($value)) return $value;
    if ($value === null || $value === '') return [];
    return [$value];
};

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

// Template path
$templatePath = __DIR__ . '/saln_file/saln_annexA.docx';
if (!file_exists($templatePath)) {
    http_response_code(500);
    echo 'Template file not found.';
    return;
}

$template = new TemplateProcessor($templatePath);

// ==========================
// Form Data
// ==========================
$first_name        = trim((string) ($_POST['first_name'] ?? ''));
$last_name         = trim((string) ($_POST['last_name'] ?? ''));
$middle_initials   = trim((string) ($_POST['middle'] ?? ''));
$position          = trim((string) ($_POST['position'] ?? ''));
$agency            = trim((string) ($_POST['agency'] ?? ''));
$address           = trim((string) ($_POST['address'] ?? ''));
$userEmailRaw      = trim((string) ($_POST['user_email'] ?? ''));
$userEmail         = filter_var($userEmailRaw, FILTER_VALIDATE_EMAIL) ? $userEmailRaw : '';
$spouse_first_name = trim((string) ($_POST['spouse_first_name'] ?? ''));
$spouse_last_name  = trim((string) ($_POST['spouse_last_name'] ?? ''));
$spouse_middle_initials = trim((string) ($_POST['spouse_middle'] ?? ''));
$spouse_position   = trim((string) ($_POST['spouse_position'] ?? ''));
$spouse_agency     = trim((string) ($_POST['spouse_agency'] ?? ''));
$spouse_address    = trim((string) ($_POST['spouse_address'] ?? ''));
$filling           = (string) ($_POST['filling'] ?? '');
$draftToken        = trim((string) ($_POST['draft_token'] ?? ''));
$children          = $toArray($_POST['children'] ?? []);
$ages              = $toArray($_POST['age'] ?? []);


// Asset info
$assets_description      = $toArray($_POST['asset_description'] ?? []);
$assets_kind             = $toArray($_POST['asset_kind'] ?? []);
$assets_location         = $toArray($_POST['asset_location'] ?? []);
$assets_value            = $toArray($_POST['asset_value'] ?? $_POST['assets_value'] ?? []);
$asset_fair_market_value = $toArray($_POST['fair_market_value'] ?? []);
$asset_acquisition_year  = $toArray($_POST['acquisition_year'] ?? $_POST['aquisition_year'] ?? []);
$asset_acquisition_mode  = $toArray($_POST['acquisition_mode'] ?? $_POST['aquisition_mode'] ?? []);
$asset_acquisition_cost  = $toArray($_POST['acquisition_cost'] ?? $_POST['aquisition_cost'] ?? []);

// ==========================
// Persist Submitted Data
// ==========================
$childrenDbRows = [];
foreach ($children as $index => $name) {
    $childName = trim((string) $name);
    $childAge = trim((string) ($ages[$index] ?? ''));

    if ($childName === '' && $childAge === '') {
        continue;
    }

    $childrenDbRows[] = [
        'children' => $childName,
        'age' => $childAge,
    ];
}

$assetDbRows = [];
$assetDbCount = max(
    count($assets_description),
    count($assets_kind),
    count($assets_location),
    count($assets_value),
    count($asset_fair_market_value),
    count($asset_acquisition_year),
    count($asset_acquisition_mode),
    count($asset_acquisition_cost)
);

for ($index = 0; $index < $assetDbCount; $index++) {
    $row = [
        'asset_description' => trim((string) ($assets_description[$index] ?? '')),
        'asset_kind' => trim((string) ($assets_kind[$index] ?? '')),
        'asset_location' => trim((string) ($assets_location[$index] ?? '')),
        'asset_value' => trim((string) ($assets_value[$index] ?? '')),
        'fair_market_value' => trim((string) ($asset_fair_market_value[$index] ?? '')),
        'acquisition_year' => trim((string) ($asset_acquisition_year[$index] ?? '')),
        'acquisition_mode' => trim((string) ($asset_acquisition_mode[$index] ?? '')),
        'acquisition_cost' => trim((string) ($asset_acquisition_cost[$index] ?? '')),
    ];

    $hasData = false;
    foreach ($row as $value) {
        if ($value !== '') {
            $hasData = true;
            break;
        }
    }

    if ($hasData) {
        $assetDbRows[] = $row;
    }
}

try {
    $userId = saln_get_or_create_user_id([
        'first_name' => $first_name,
        'middle_name' => $middle_initials,
        'last_name' => $last_name,
        'email' => $userEmail,
    ], $draftToken);

    saln_db_upsert_user_profile($userId, [
        'first_name' => $first_name,
        'middle_name' => $middle_initials,
        'last_name' => $last_name,
        'position_title' => $position,
        'agency' => $agency,
        'office_address' => $address,
        'email' => $userEmail,
        'spouse_first_name' => $spouse_first_name,
        'spouse_middle_name' => $spouse_middle_initials,
        'spouse_last_name' => $spouse_last_name,
        'spouse_position' => $spouse_position,
        'spouse_agency' => $spouse_agency,
        'spouse_office_address' => $spouse_address,
    ]);

    saln_db_save_annex_form($userId, 'A', 'index.php', $_POST, $filling);
    saln_db_replace_section_rows($userId, 'A', 'annexAChildren', $childrenDbRows);
    saln_db_replace_section_rows($userId, 'A', 'annexAAssets', $assetDbRows);
} catch (Throwable $dbException) {
    error_log('SALN Annex A DB save failed: ' . $dbException->getMessage());
}

// ==========================
// File Name Sanitization
// ==========================
$sanitizeForFileName = static function (string $value): string {
    return trim(preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($value))), '_');
};

$fileNamePrefix = trim(
    $sanitizeForFileName($first_name) . '_' . $sanitizeForFileName($last_name),
    '_'
);
$fileNamePrefix = $fileNamePrefix ?: 'declarant';
$fileName = $fileNamePrefix . '_saln_annexA.docx';

// ==========================
// Populate Template
// ==========================
$template->setValue('first_name', htmlspecialchars($first_name));
$template->setValue('family_name', htmlspecialchars($last_name));
$template->setValue('middle', htmlspecialchars($middle_initials));
$template->setValue('position', htmlspecialchars($position));
$template->setValue('agency', htmlspecialchars($agency));
$template->setValue('address', nl2br(htmlspecialchars($address)));

$template->setValue('s_first_name', htmlspecialchars($spouse_first_name));
$template->setValue('s_fam_n', htmlspecialchars($spouse_last_name));
$template->setValue('s_middle', htmlspecialchars($spouse_middle_initials));
$template->setValue('s_position', htmlspecialchars($spouse_position));
$template->setValue('s_agency', htmlspecialchars($spouse_agency));
$template->setValue('s_address', nl2br(htmlspecialchars($spouse_address)));

$template->setValue('joint', $filling === 'joint' ? '☑' : '☐');
$template->setValue('separate', $filling === 'separate' ? '☑' : '☐');
$template->setValue('na', $filling === 'na' ? '☑' : '☐');

// Children
$childRows = [];
foreach ($children as $i => $name) {
    $n = trim((string) $name);
    $a = $ages[$i] ?? '';
    if ($n === '' && $a === '') continue;
    $childRows[] = ['children' => htmlspecialchars($n), 'child_age' => htmlspecialchars($a)];
}

$variables = $template->getVariables();
if (in_array('children', $variables, true)) {
    if (count($childRows) > 0) $template->cloneRowAndSetValues('children', $childRows);
    else {
        $template->setValue('children', '');
        $template->setValue('child_age', '');
    }
}

// Assets
$assetRows = [];
$assetRowCount = max(
    count($assets_description),
    count($assets_kind),
    count($assets_location),
    count($assets_value),
    count($asset_fair_market_value),
    count($asset_acquisition_year),
    count($asset_acquisition_mode),
    count($asset_acquisition_cost)
);

for ($i = 0; $i < $assetRowCount; $i++) {
    $desc  = trim((string) ($assets_description[$i] ?? ''));
    $kind  = trim((string) ($assets_kind[$i] ?? ''));
    $loc   = trim((string) ($assets_location[$i] ?? ''));
    $val   = trim((string) ($assets_value[$i] ?? ''));
    $fmv   = trim((string) ($asset_fair_market_value[$i] ?? ''));
    $year  = trim((string) ($asset_acquisition_year[$i] ?? ''));
    $mode  = trim((string) ($asset_acquisition_mode[$i] ?? ''));
    $ac    = trim((string) ($asset_acquisition_cost[$i] ?? ''));

    if ($desc === '' && $kind === '' && $loc === '' && $val === '' && $fmv === '' && $year === '' && $mode === '' && $ac === '') continue;

    $assetRows[] = [
        'description'     => htmlspecialchars($desc),
        'kind'            => htmlspecialchars($kind),
        'exact_location'  => htmlspecialchars($loc),
        'assessed_value'  => htmlspecialchars($val),
        'cfmv'            => htmlspecialchars($fmv),
        'year'            => htmlspecialchars($year),
        'mode'            => htmlspecialchars($mode),
        'ac'              => htmlspecialchars($ac),
    ];
}

if (in_array('description', $variables, true)) {
    if (count($assetRows) > 0) $template->cloneRowAndSetValues('description', $assetRows);
    else {
        $template->setValue('description', '');
        $template->setValue('kind', '');
        $template->setValue('exact_location', '');
        $template->setValue('assessed_value', '');
        $template->setValue('cfmv', '');
        $template->setValue('year', '');
        $template->setValue('mode', '');
        $template->setValue('ac', '');
    }
}

// Save output
$outputFile = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
$template->saveAs($outputFile);

// ==========================
// Send Email via PHPMailer
// ==========================
$smtpHost       = $getEnv('SMTP_HOST');
$smtpUser       = $getEnv('SMTP_USERNAME', $getEnv('SMTP_USER'));
$smtpPass       = $getEnv('SMTP_PASSWORD', $getEnv('SMTP_PASS'));
$smtpPort       = (int) $getEnv('SMTP_PORT', '587');
$smtpFrom       = $getEnv('SMTP_FROM_EMAIL', $getEnv('SMTP_FROM', $smtpUser));
$smtpFromName   = $getEnv('SMTP_FROM_NAME', 'SALN Generator');
$smtpEncryption = strtolower($getEnv('SMTP_ENCRYPTION', 'tls'));

// Gmail app passwords are often stored with spaces for readability.
if (stripos($smtpHost, 'gmail.com') !== false) {
    $smtpPass = preg_replace('/\s+/', '', $smtpPass);
}

$logSmtpMessage = static function (string $message): void {
    error_log($message);
    @file_put_contents(
        __DIR__ . DIRECTORY_SEPARATOR . 'smtp_debug.log',
        '[' . date('c') . '] ' . $message . PHP_EOL,
        FILE_APPEND
    );
};

if ($userEmail && $smtpHost && $smtpUser && $smtpPass && $smtpFrom) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = $smtpEncryption === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtpPort;
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 30;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom($smtpFrom, $smtpFromName);
        $mail->addAddress($userEmail);
        $mail->isHTML(true);
        $mail->Subject = 'SALN Annex A Submission - ' . trim("$first_name $middle_initials $last_name");

        $mail->Body = '<p>Your SALN Annex A document has been generated. See attached.</p>';
        $mail->AltBody = 'Your SALN Annex A document has been generated. See attached.';

        if (file_exists($outputFile)) $mail->addAttachment($outputFile, basename($outputFile));

        $mail->send();
        $logSmtpMessage('SALN SMTP sent successfully to ' . $userEmail);
    } catch (Exception $e) {
        $logSmtpMessage('SALN SMTP send failed: ' . $mail->ErrorInfo . ' | Exception: ' . $e->getMessage());
    }
} else {
    $logSmtpMessage(
        'SALN SMTP skipped. Missing fields: '
            . 'userEmail=' . ($userEmail !== '' ? 'yes' : 'no') . ', '
            . 'smtpHost=' . ($smtpHost !== '' ? 'yes' : 'no') . ', '
            . 'smtpUser=' . ($smtpUser !== '' ? 'yes' : 'no') . ', '
            . 'smtpPass=' . ($smtpPass !== '' ? 'yes' : 'no') . ', '
            . 'smtpFrom=' . ($smtpFrom !== '' ? 'yes' : 'no')
    );
}

// ==========================
// Deliver File to Browser
// ==========================
if (file_exists($outputFile)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputFile));
    readfile($outputFile);
    exit;
}

http_response_code(500);
echo 'Unable to generate SALN document.';
