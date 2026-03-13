<?php

require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $templatePath = __DIR__ . '/saln_file/saln.docx';
    $template = new TemplateProcessor($templatePath);

    //Declarant Information
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_initials = $_POST['middle'];
    $position = $_POST['position'];
    $agency = $_POST['agency'];
    $address = $_POST['address'];

    $file_name = strtolower($first_name . '_' . $last_name . '_saln_.docx');

    //Spouse Information
    $spouse_first_name = $_POST['spouse_first_name'];
    $spouse_last_name = $_POST['spouse_last_name'];
    $spouse_middle_initials = $_POST['spouse_middle'];
    $spouse_position = $_POST['spouse_position'];
    $spouse_agency = $_POST['spouse_agency'];
    $spouse_address = $_POST['spouse_address'];

    //SPOUSES, WHO ARE BOTH PUBLIC OFFICIALS OR EMPLOYEES, MAY FILE THE SALN JOINTLY OR SEPARATELY. THE DECLARANT SHALL CHECK THE APPROPRIATE BOX
    $filling = $_POST['filling'];

    $children = $_POST['children'] ?? [];


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
