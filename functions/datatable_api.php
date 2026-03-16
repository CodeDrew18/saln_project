<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(bool $success, string $message = '', array $data = [], ?int $statusCode = null): void
{
    if ($statusCode === null) {
        $statusCode = $success ? 200 : 400;
    }

    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
    ]);
    exit;
}

function sanitizeRow(array $input, array $allowedFields): array
{
    $clean = [];

    foreach ($allowedFields as $field) {
        $value = $input[$field] ?? '';
        if (is_scalar($value)) {
            $value = trim((string) $value);
        } else {
            $value = '';
        }

        $clean[$field] = substr($value, 0, 5000);
    }

    return $clean;
}

function rowHasData(array $row): bool
{
    foreach ($row as $value) {
        if ($value !== '') {
            return true;
        }
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Only POST requests are allowed.');
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput ?: '', true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$allowedFieldsByType = [
    'annexAChildren' => ['children', 'age'],
    'annexAAssets' => ['asset_description', 'asset_kind', 'asset_location', 'asset_value', 'fair_market_value', 'acquisition_year', 'acquisition_mode', 'acquisition_cost'],
    'annexBRealProperty' => ['real_property_description', 'real_property_kind', 'real_property_location', 'real_property_assessed_value', 'real_property_fair_market_value', 'real_property_acquisition_year', 'real_property_acquisition_mode', 'real_property_acquisition_cost'],
    'annexBPersonalProperty' => ['personal_property_description', 'personal_property_acquisition_year', 'personal_property_amount'],
    'annexBLiability' => ['liability_nature', 'liability_creditor', 'liability_balance'],
    'annexBBusinessInterest' => ['business_entity_name', 'business_address', 'business_interest_nature', 'business_interest_acquisition_date'],
    'annexCChildren' => ['annexc_child_name', 'annexc_child_age', 'annexc_child_relationship'],
    'annexCRealProperty' => ['annexc_real_description', 'annexc_real_kind', 'annexc_real_location', 'annexc_real_assessed_value', 'annexc_real_fair_market_value', 'annexc_real_acquisition_year', 'annexc_real_acquisition_mode', 'annexc_real_acquisition_cost'],
    'annexCPersonalProperty' => ['annexc_personal_description', 'annexc_personal_acquisition_year', 'annexc_personal_amount'],
    'annexCBusinessInterest' => ['annexc_business_entity_name', 'annexc_business_address', 'annexc_business_nature', 'annexc_business_acquisition_date'],
];

$action = isset($payload['action']) ? trim((string) $payload['action']) : '';
$type = isset($payload['type']) ? trim((string) $payload['type']) : '';
$draftToken = isset($payload['draft_token']) ? trim((string) $payload['draft_token']) : '';

if ($action === '' || $type === '') {
    jsonResponse(false, 'Missing action or type.');
}

if (!array_key_exists($type, $allowedFieldsByType)) {
    jsonResponse(false, 'Invalid table type.');
}

try {
    $allowedFields = $allowedFieldsByType[$type];
    $userId = saln_get_or_create_user_id([], $draftToken);
    $annexCode = saln_db_section_annex_code($type);

    switch ($action) {
        case 'list':
            $rows = saln_db_list_rows($userId, $type, $allowedFields);
            jsonResponse(true, 'Rows loaded.', $rows);
            break;

        case 'create':
            $input = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : [];
            $newRow = sanitizeRow($input, $allowedFields);

            if (!rowHasData($newRow)) {
                jsonResponse(false, 'Cannot add an empty entry.');
            }

            $createdRow = saln_db_insert_row($userId, $annexCode, $type, $newRow);
            jsonResponse(true, 'Entry created.', $createdRow);
            break;

        case 'update':
            $id = isset($payload['id']) ? trim((string) $payload['id']) : '';
            if ($id === '') {
                jsonResponse(false, 'Missing row ID for update.');
            }

            $input = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : [];
            $updatedRow = sanitizeRow($input, $allowedFields);

            if (!rowHasData($updatedRow)) {
                jsonResponse(false, 'Cannot update with empty values only.');
            }

            $didUpdate = saln_db_update_row($userId, $annexCode, $type, $id, $updatedRow);
            if (!$didUpdate) {
                jsonResponse(false, 'Entry not found for update.');
            }

            $updatedRow['id'] = $id;
            jsonResponse(true, 'Entry updated.', $updatedRow);
            break;

        case 'delete':
            $id = isset($payload['id']) ? trim((string) $payload['id']) : '';
            if ($id === '') {
                jsonResponse(false, 'Missing row ID for delete.');
            }

            $didDelete = saln_db_delete_row($userId, $type, $id);
            if (!$didDelete) {
                jsonResponse(false, 'Entry not found for delete.');
            }

            jsonResponse(true, 'Entry deleted.', ['id' => $id]);
            break;

        case 'clear':
            saln_db_clear_rows($userId, $type);
            jsonResponse(true, 'Entries cleared.', []);
            break;

        default:
            jsonResponse(false, 'Invalid action.');
    }
} catch (Throwable $exception) {
    error_log('datatable_api error: ' . $exception->getMessage());
    jsonResponse(false, 'Database operation failed.', [], 500);
}
