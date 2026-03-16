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


    <main class="main-content">
        <form action="saln_download.php" method="POST" id="salnForm">
            <button class="btn-secondary btn-clear" type="button" onclick="clearForm(event)">
                Clear All Fields
            </button>

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

            <!-- Declarant Information -->
            <section class="form-section">
                <header class="section-header">
                    <h2 class="section-title">
                        <span class="section-number">1</span>
                        Declarant Information
                    </h2>
                </header>
                <div class="section-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name <span class="form-required">*</span></label>
                            <input class="form-input" type="text" id="first_name" name="first_name" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group form-group--compact">
                            <label class="form-label" for="middle">Middle Initials</label>
                            <input class="form-input" type="text" id="middle" name="middle" placeholder="e.g., M.I.">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name <span class="form-required">*</span></label>
                            <input class="form-input" type="text" id="last_name" name="last_name" placeholder="Enter last name" required>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="position">Position <span class="form-required">*</span></label>
                            <input class="form-input" type="text" id="position" name="position" placeholder="e.g., Administrative Officer III" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="agency">Agency / Office <span class="form-required">*</span></label>
                            <input class="form-input" type="text" id="agency" name="agency" placeholder="Enter agency or office name" required>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Assets, Real Properties, and Personal Properties -->
            <section class="form-section">
                <header class="section-header">
                    <h2 class="section-title">
                        <span class="section-number">2</span>
                        Assets, Real Properties, and Personal Properties
                    </h2>
                    <button class="btn-add" type="button" onclick="addAsset()">
                        + Add Asset
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per asset so each entry is displayed in the generated Word document.</p>
                    <div class="children-container" id="assetContainer">
                        <div class="child-entry asset-entry">
                            <button class="btn-remove" type="button" onclick="removeAsset(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <input class="form-input" type="text" name="asset_description[]" placeholder="Describe the asset">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kind</label>
                                    <input class="form-input" type="text" name="asset_kind[]" placeholder="e.g., Land, Building, Vehicle">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Exact Location</label>
                                    <input class="form-input" type="text" name="asset_location[]" placeholder="Location of the asset">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Asset Value</label>
                                    <input class="form-input" type="text" name="asset_value[]" placeholder="Current value">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Current Fair Market Value</label>
                                    <input class="form-input" type="text" name="fair_market_value[]" placeholder="Market value">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Year of Acquisition</label>
                                    <input class="form-input" type="number" name="acquisition_year[]" placeholder="YYYY">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Acquisition Mode</label>
                                    <input class="form-input" type="text" name="acquisition_mode[]" placeholder="e.g., Purchase, Donation">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Acquisition Cost</label>
                                    <input class="form-input" type="text" name="acquisition_cost[]" placeholder="Cost when acquired">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Personal Properties -->
            <section class="form-section">
                <header class="section-header">
                    <h2 class="section-title">
                        <span class="section-number">3</span>
                        Personal Properties

                    </h2>
                    <button class="btn-add" type="button" onclick="addAsset()">
                        + Add Asset
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per asset so each entry is displayed in the generated Word document.</p>
                    <div class="children-container" id="assetContainer">
                        <div class="child-entry asset-entry">
                            <button class="btn-remove" type="button" onclick="removeAsset(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <input class="form-input" type="text" name="asset_description[]" placeholder="Describe the asset">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Year of Acquisition</label>
                                    <input class="form-input" type="number" name="acquisition_year[]" placeholder="YYYY">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Acquisition Cost / Amount</label>
                                    <input class="form-input" type="text" name="acquisition_cost[]" placeholder="Cost when acquired">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Liabilities -->

            <section class="form-section">
                <header class="section-header">
                    <h2 class="section-title">
                        <span class="section-number">4</span>
                        Liabilities

                    </h2>
                    <button class="btn-add" type="button" onclick="addAsset()">
                        + Add Liabilities
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per asset so each entry is displayed in the generated Word document.</p>
                    <div class="children-container" id="assetContainer">
                        <div class="child-entry asset-entry">
                            <button class="btn-remove" type="button" onclick="removeAsset(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Nature</label>
                                    <input class="form-input" type="text" name="asset_description[]" placeholder="Describe the Nature">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Name of Creditors</label>
                                    <input class="form-input" type="text" name="acquisition_year[]" placeholder="Creditor's Name">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Outstanding Balance</label>
                                    <input class="form-input" type="text" name="acquisition_cost[]" placeholder="Outstanding Balance">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Business Interests and Financial Connections -->

            <section class="form-section">
                <header class="section-header">
                    <h2 class="section-title">
                        <span class="section-number">5</span>
                        Business Interests and Financial Connections

                    </h2>
                    <button class="btn-add" type="button" onclick="addAsset()">
                        + Add Business Interests and Financial Connections
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per asset so each entry is displayed in the generated Word document.</p>
                    <div class="children-container" id="assetContainer">
                        <div class="child-entry asset-entry">
                            <button class="btn-remove" type="button" onclick="removeAsset(this)" title="Remove entry">&times;</button>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Name of Entity / Business Enterprise</label>
                                    <input class="form-input" type="text" name="asset_description[]" placeholder="Name of the Entity">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Business Address</label>
                                    <input class="form-input" type="text" name="acquisition_year[]" placeholder="Address">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nature of Business Interest &/ or Financial Connection</label>
                                    <input class="form-input" type="text" name="acquisition_cost[]" placeholder="Cost when acquired">
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Date of Acquisition of Interest or Connection</label>
                                        <input class="form-input" type="date" name="acquisition_cost[]" placeholder="Cost when acquired">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>



    </main>

    <?php include 'template/footer.php'; ?>


    <script src="functions/index.js"></script>

</body>

</html>