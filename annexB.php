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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="style.css?v=fullwidth-20260316">
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
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

            <!-- Real Properties -->
            <section class="form-section">
                <header class="section-header">
                    <h2 class="section-title">
                        <span class="section-number">2</span>
                        Real Properties
                    </h2>
                    <button class="btn-add" type="button" onclick="addAnnexBRealProperty()">
                        + Add Real Property
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per real property declared by the declarant.</p>
                    <div class="children-container" id="annexBRealPropertyContainer">
                        <div class="child-entry asset-entry annexb-real-entry">
                            <button class="btn-remove" type="button" onclick="removeAnnexBRealProperty(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <input class="form-input" type="text" name="real_property_description[]" placeholder="Describe the real property">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kind</label>
                                    <input class="form-input" type="text" name="real_property_kind[]" placeholder="e.g., Land, Building, Condominium Unit">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Exact Location</label>
                                    <input class="form-input" type="text" name="real_property_location[]" placeholder="Location of the real property">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Assessed Value</label>
                                    <input class="form-input" type="text" name="real_property_assessed_value[]" placeholder="Assessed value">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Current Fair Market Value</label>
                                    <input class="form-input" type="text" name="real_property_fair_market_value[]" placeholder="Current fair market value">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Year Acquired</label>
                                    <input class="form-input" type="number" name="real_property_acquisition_year[]" placeholder="YYYY">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Mode of Acquisition</label>
                                    <input class="form-input" type="text" name="real_property_acquisition_mode[]" placeholder="e.g., Purchase, Inheritance, Donation">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Acquisition Cost</label>
                                    <input class="form-input" type="text" name="real_property_acquisition_cost[]" placeholder="Cost at the time of acquisition">
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
                    <button class="btn-add" type="button" onclick="addAnnexBPersonalProperty()">
                        + Add Personal Property
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per personal property declared by the declarant.</p>
                    <div class="children-container" id="annexBPersonalPropertyContainer">
                        <div class="child-entry asset-entry annexb-personal-entry">
                            <button class="btn-remove" type="button" onclick="removeAnnexBPersonalProperty(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <input class="form-input" type="text" name="personal_property_description[]" placeholder="Describe the personal property">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Year Acquired</label>
                                    <input class="form-input" type="number" name="personal_property_acquisition_year[]" placeholder="YYYY">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Acquisition Cost / Amount</label>
                                    <input class="form-input" type="text" name="personal_property_amount[]" placeholder="Acquisition amount">
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
                    <button class="btn-add" type="button" onclick="addAnnexBLiability()">
                        + Add Liability
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per liability with the creditor and outstanding balance.</p>
                    <div class="children-container" id="annexBLiabilityContainer">
                        <div class="child-entry asset-entry annexb-liability-entry">
                            <button class="btn-remove" type="button" onclick="removeAnnexBLiability(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Nature</label>
                                    <input class="form-input" type="text" name="liability_nature[]" placeholder="e.g., Housing Loan, Personal Loan">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Name of Creditor</label>
                                    <input class="form-input" type="text" name="liability_creditor[]" placeholder="Creditor name">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Outstanding Balance</label>
                                    <input class="form-input" type="text" name="liability_balance[]" placeholder="Outstanding balance">
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
                    <button class="btn-add" type="button" onclick="addAnnexBBusinessInterest()">
                        + Add Business Interest
                    </button>
                </header>
                <div class="section-body">
                    <p class="section-note">Add one row per declared business interest or financial connection.</p>
                    <div class="children-container" id="annexBBusinessContainer">
                        <div class="child-entry asset-entry annexb-business-entry">
                            <button class="btn-remove" type="button" onclick="removeAnnexBBusinessInterest(this)" title="Remove entry">&times;</button>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Name of Entity / Business Enterprise</label>
                                    <input class="form-input" type="text" name="business_entity_name[]" placeholder="Name of entity or enterprise">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Business Address</label>
                                    <input class="form-input" type="text" name="business_address[]" placeholder="Business address">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group form-group--full">
                                    <label class="form-label">Nature of Business Interest and/or Financial Connection</label>
                                    <input class="form-input" type="text" name="business_interest_nature[]" placeholder="Describe the business interest or financial connection">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Date of Acquisition of Interest or Connection</label>
                                    <input class="form-input" type="date" name="business_interest_acquisition_date[]">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button class="btn-primary" type="submit">
                    Generate SALN Document
                </button>
            </div>
        </form>
    </main>

    <?php include 'template/footer.php'; ?>


    <script src="functions/index.js"></script>

</body>

</html>