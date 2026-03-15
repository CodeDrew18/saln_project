<?php

require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $templatePath = __DIR__ . '/saln_file/saln_annexA.docx';
    $template = new TemplateProcessor($templatePath);

    //Declarant Information
    $first_name = $_POST['first_name'] ?? "";
    $last_name = $_POST['last_name'] ?? "";
    $middle_initials = $_POST['middle'] ?? "";
    $position = $_POST['position'] ?? "";
    $agency = $_POST['agency'] ?? "";
    $address = $_POST['address'] ?? "";

    $file_name = strtolower($first_name . '_' . $last_name . '_saln_annexA.docx');

    //Spouse Information
    $spouse_first_name = $_POST['spouse_first_name'] ?? "";
    $spouse_last_name = $_POST['spouse_last_name'] ?? "";
    $spouse_middle_initials = $_POST['spouse_middle'] ?? "";
    $spouse_position = $_POST['spouse_position'] ?? "";
    $spouse_agency = $_POST['spouse_agency'] ?? "";
    $spouse_address = $_POST['spouse_address'] ?? "";

    //SPOUSES, WHO ARE BOTH PUBLIC OFFICIALS OR EMPLOYEES, MAY FILE THE SALN JOINTLY OR SEPARATELY. THE DECLARANT SHALL CHECK THE APPROPRIATE BOX
    $filling = $_POST['filling'] ?? '';

    $toArray = static function ($value): array {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return [];
        }

        return [$value];
    };

    $children = $toArray($_POST['children'] ?? []);
    $ages = $toArray($_POST['age'] ?? []);

    // asset information
    $assets_description = $toArray($_POST['asset_description'] ?? []);
    $assets_kind = $toArray($_POST['asset_kind'] ?? []);
    $assets_location = $toArray($_POST['asset_location'] ?? []);
    $assets_value = $toArray($_POST['asset_value'] ?? $_POST['assets_value'] ?? []);
    $asset_fair_market_value = $toArray($_POST['fair_market_value'] ?? []);
    $asset_acquisition_year = $toArray($_POST['acquisition_year'] ?? $_POST['aquisition_year'] ?? []);
    $asset_acquisition_mode = $toArray($_POST['acquisition_mode'] ?? $_POST['aquisition_mode'] ?? []);
    $asset_acquisition_cost = $toArray($_POST['acquisition_cost'] ?? $_POST['aquisition_cost'] ?? []);




    // Delaration Information
    $template->setValue('first_name', htmlspecialchars($first_name));
    $template->setValue('family_name', htmlspecialchars($last_name));
    $template->setValue('middle', htmlspecialchars($middle_initials));

    $template->setValue('position', htmlspecialchars($position));
    $template->setValue('agency', htmlspecialchars($agency));
    $template->setValue('address', nl2br(htmlspecialchars($address)));

    // Spouse Information
    $template->setValue('s_first_name', htmlspecialchars($spouse_first_name));
    $template->setValue('s_fam_n', htmlspecialchars($spouse_last_name));
    $template->setValue('s_middle', htmlspecialchars($spouse_middle_initials));

    $template->setValue('s_position', htmlspecialchars($spouse_position));
    $template->setValue('s_agency', htmlspecialchars($spouse_agency));
    $template->setValue('s_address', nl2br(htmlspecialchars($spouse_address)));

    //SPOUSES, WHO ARE BOTH PUBLIC OFFICIALS OR EMPLOYEES, MAY FILE THE SALN JOINTLY OR SEPARATELY. THE DECLARANT SHALL CHECK THE APPROPRIATE BOX

    $template->setValue('joint', $filling == 'joint' ? '☑' : '☐');
    $template->setValue('separate', $filling == 'separate' ? '☑' : '☐');
    $template->setValue('na', $filling == 'na' ? '☑' : '☐');


    // Children Information
    $childRows = [];
    foreach ($children as $index => $childName) {
        $name = trim($childName);
        $age = isset($ages[$index]) ? trim((string) $ages[$index]) : '';

        if ($name === '' && $age === '') {
            continue;
        }

        $childRows[] = [
            'children' => htmlspecialchars($name),
            'child_age' => htmlspecialchars($age),
        ];
    }

    $variables = $template->getVariables();
    if (in_array('children', $variables, true)) {
        if (count($childRows) > 0) {
            $template->cloneRowAndSetValues('children', $childRows);
        } else {
            $template->setValue('children', '');
            $template->setValue('child_age', '');
        }
    }

    // Asset Information
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

    for ($index = 0; $index < $assetRowCount; $index++) {
        $description = trim((string) ($assets_description[$index] ?? ''));
        $kind = trim((string) ($assets_kind[$index] ?? ''));
        $location = trim((string) ($assets_location[$index] ?? ''));
        $assessedValue = trim((string) ($assets_value[$index] ?? ''));
        $fairMarketValue = trim((string) ($asset_fair_market_value[$index] ?? ''));
        $year = trim((string) ($asset_acquisition_year[$index] ?? ''));
        $mode = trim((string) ($asset_acquisition_mode[$index] ?? ''));
        $acquisitionCost = trim((string) ($asset_acquisition_cost[$index] ?? ''));

        if (
            $description === '' &&
            $kind === '' &&
            $location === '' &&
            $assessedValue === '' &&
            $fairMarketValue === '' &&
            $year === '' &&
            $mode === '' &&
            $acquisitionCost === ''
        ) {
            continue;
        }

        $assetRows[] = [
            'description' => htmlspecialchars($description),
            'kind' => htmlspecialchars($kind),
            'exact_location' => htmlspecialchars($location),
            'assessed_value' => htmlspecialchars($assessedValue),
            'cfmv' => htmlspecialchars($fairMarketValue),
            'year' => htmlspecialchars($year),
            'mode' => htmlspecialchars($mode),
            'ac' => htmlspecialchars($acquisitionCost),
        ];
    }

    if (in_array('description', $variables, true)) {
        if (count($assetRows) > 0) {
            $template->cloneRowAndSetValues('description', $assetRows);
        } else {
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

    



    $outputFile =  $file_name;
    $template->saveAs($outputFile);

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
}
