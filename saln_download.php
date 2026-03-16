<?php

declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed.';
    exit;
}

$annexType = trim((string) ($_POST['annex_type'] ?? ''));
$targetPage = 'index.php';
$annexCode = 'A';

if (strcasecmp($annexType, 'annexB.php') === 0) {
    $targetPage = 'annexB.php';
    $annexCode = 'B';
} elseif (strcasecmp($annexType, 'annexC.php') === 0) {
    $targetPage = 'annexC.php';
    $annexCode = 'C';
}

if ($annexCode === 'A') {
    // Fallback detection by annex-specific field names.
    if (isset($_POST['annexc_spouse_first_name']) || isset($_POST['annexc_child_name'])) {
        $annexCode = 'C';
        $targetPage = 'annexC.php';
    } elseif (isset($_POST['real_property_description']) || isset($_POST['liability_nature'])) {
        $annexCode = 'B';
        $targetPage = 'annexB.php';
    }
}

$sectionMapByAnnex = [
    'B' => [
        'annexBRealProperty' => [
            'real_property_description',
            'real_property_kind',
            'real_property_location',
            'real_property_assessed_value',
            'real_property_fair_market_value',
            'real_property_acquisition_year',
            'real_property_acquisition_mode',
            'real_property_acquisition_cost',
        ],
        'annexBPersonalProperty' => [
            'personal_property_description',
            'personal_property_acquisition_year',
            'personal_property_amount',
        ],
        'annexBLiability' => [
            'liability_nature',
            'liability_creditor',
            'liability_balance',
        ],
        'annexBBusinessInterest' => [
            'business_entity_name',
            'business_address',
            'business_interest_nature',
            'business_interest_acquisition_date',
        ],
    ],
    'C' => [
        'annexCChildren' => [
            'annexc_child_name',
            'annexc_child_age',
            'annexc_child_relationship',
        ],
        'annexCRealProperty' => [
            'annexc_real_description',
            'annexc_real_kind',
            'annexc_real_location',
            'annexc_real_assessed_value',
            'annexc_real_fair_market_value',
            'annexc_real_acquisition_year',
            'annexc_real_acquisition_mode',
            'annexc_real_acquisition_cost',
        ],
        'annexCPersonalProperty' => [
            'annexc_personal_description',
            'annexc_personal_acquisition_year',
            'annexc_personal_amount',
        ],
        'annexCBusinessInterest' => [
            'annexc_business_entity_name',
            'annexc_business_address',
            'annexc_business_nature',
            'annexc_business_acquisition_date',
        ],
    ],
];

try {
    $draftToken = trim((string) ($_POST['draft_token'] ?? ''));

    $defaultProfile = [
        'first_name' => trim((string) ($_POST['first_name'] ?? '')),
        'middle_name' => trim((string) ($_POST['middle'] ?? '')),
        'last_name' => trim((string) ($_POST['last_name'] ?? '')),
        'email' => trim((string) ($_POST['user_email'] ?? '')),
    ];

    $userId = saln_get_or_create_user_id($defaultProfile, $draftToken);

    $profile = [
        'first_name' => trim((string) ($_POST['first_name'] ?? '')),
        'middle_name' => trim((string) ($_POST['middle'] ?? '')),
        'last_name' => trim((string) ($_POST['last_name'] ?? '')),
        'position_title' => trim((string) ($_POST['position'] ?? '')),
        'agency' => trim((string) ($_POST['agency'] ?? '')),
        'office_address' => trim((string) ($_POST['address'] ?? '')),
        'email' => trim((string) ($_POST['user_email'] ?? '')),
        'spouse_first_name' => trim((string) ($_POST['annexc_spouse_first_name'] ?? '')),
        'spouse_middle_name' => trim((string) ($_POST['annexc_spouse_middle'] ?? '')),
        'spouse_last_name' => trim((string) ($_POST['annexc_spouse_last_name'] ?? '')),
        'spouse_position' => trim((string) ($_POST['annexc_spouse_position'] ?? '')),
        'spouse_agency' => trim((string) ($_POST['annexc_spouse_agency'] ?? '')),
        'spouse_office_address' => trim((string) ($_POST['annexc_spouse_address'] ?? '')),
    ];

    saln_db_upsert_user_profile($userId, $profile);
    saln_db_save_annex_form($userId, $annexCode, $targetPage, $_POST, trim((string) ($_POST['filling'] ?? '')));

    $sectionMap = $sectionMapByAnnex[$annexCode] ?? [];
    foreach ($sectionMap as $sectionKey => $fields) {
        $rows = saln_db_rows_from_post($_POST, $fields);
        saln_db_replace_section_rows($userId, $annexCode, $sectionKey, $rows);
    }

    header('Location: ' . $targetPage . '?saved=1');
    exit;
} catch (Throwable $exception) {
    error_log('SALN submit DB save failed: ' . $exception->getMessage());
    http_response_code(500);
    echo 'Unable to save form data to database.';
    exit;
}
