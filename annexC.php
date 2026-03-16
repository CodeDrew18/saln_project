<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALN Generator - Statement of Assets, Liabilities, and Net Worth</title>
    <link rel="icon" href="favicon/saln_generator.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'template/header.php'; ?>

    <div class="form-grid">
        <div class="form-group form-group--full">
            <label class="form-label" for="annex_type">SALN Form Annex</label>
            <select class="form-select" id="annex_type" name="annex_type" onchange="navigateToAnnexPage()">
                <option value="index.php" selected>1-A Rules Annex A_2025 SALN Form (Updated Form as of 3 February 2026)</option>
                <option value="annexB.php">1-B Rules Annex B_2025 SALN Form AS-1 (Declarant) (Updated Form as of 3 February 2026)</option>
                <option value="annexC.php">1-C Rules Annex C_ 2025 SALN Form AS-2 (Spouse and Children) (Updated Form as of 3 February 2026)</option>
            </select>
        </div>
    </div>


    <?php include 'template/footer.php'; ?>
    <script src="functions/index.js"></script>
</body>

</html>