<?php
include_once 'saln_download.php';
?>

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
    <div class="page-wrapper">
        <!-- Header Section -->
        <header class="header-section">
            <div class="header-content">
                <span class="header-label">Republic of the Philippines</span>
                <h1 class="header-title">Statement of Assets, Liabilities, and Net Worth</h1>
                <p class="header-subtitle">SALN Form Generator</p>
                <p class="header-description">Official Declaration Form for Public Officials and Employees</p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <form action="saln_download.php" method="POST" id="salnForm">

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
                        <div class="form-grid">
                            <div class="form-group form-group--full">
                                <label class="form-label" for="address">Office Address <span class="form-required">*</span></label>
                                <input class="form-input" type="text" id="address" name="address" placeholder="Enter complete office address" required>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Spouse Information -->
                <section class="form-section">
                    <header class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">2</span>
                            Spouse Information
                        </h2>
                    </header>
                    <div class="section-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="spouse_first_name">First Name</label>
                                <input class="form-input" type="text" id="spouse_first_name" name="spouse_first_name" placeholder="Enter first name">
                            </div>
                            <div class="form-group form-group--compact">
                                <label class="form-label" for="spouse_middle">Middle Initials</label>
                                <input class="form-input" type="text" id="spouse_middle" name="spouse_middle" placeholder="e.g., M.I.">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="spouse_last_name">Last Name</label>
                                <input class="form-input" type="text" id="spouse_last_name" name="spouse_last_name" placeholder="Enter last name">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="spouse_position">Position</label>
                                <input class="form-input" type="text" id="spouse_position" name="spouse_position" placeholder="e.g., Teacher I">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="spouse_agency">Agency / Office</label>
                                <input class="form-input" type="text" id="spouse_agency" name="spouse_agency" placeholder="Enter agency or office name">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group form-group--full">
                                <label class="form-label" for="spouse_address">Office Address</label>
                                <input class="form-input" type="text" id="spouse_address" name="spouse_address" placeholder="Enter complete office address">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Filing Status -->
                <section class="form-section">
                    <header class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">3</span>
                            Filing Status
                        </h2>
                    </header>
                    <div class="section-body">
                        <p class="section-note">Spouses who are both public officials or employees may file the SALN jointly or separately. Select the appropriate filing status:</p>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input class="radio-input" type="radio" id="joint" name="filling" value="joint">
                                <label class="radio-label" for="joint">
                                    <span class="radio-dot"></span>
                                    <span class="radio-text">Joint Filing</span>
                                </label>
                            </div>
                            <div class="radio-item">
                                <input class="radio-input" type="radio" id="separate" name="filling" value="separate">
                                <label class="radio-label" for="separate">
                                    <span class="radio-dot"></span>
                                    <span class="radio-text">Separate Filing</span>
                                </label>
                            </div>
                            <div class="radio-item">
                                <input class="radio-input" type="radio" id="na" name="filling" value="na">
                                <label class="radio-label" for="na">
                                    <span class="radio-dot"></span>
                                    <span class="radio-text">Not Applicable</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Children Information -->
                <section class="form-section">
                    <header class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">4</span>
                            Children Information
                        </h2>
                        <button class="btn-add" type="button" onclick="addChild()">
                            + Add Child
                        </button>
                    </header>
                    <div class="section-body">
                        <p class="section-note">List unmarried children below eighteen (18) years of age living in declarant''s household:</p>
                        <div class="children-container" id="childrenContainer">
                            <div class="child-entry">
                                <button class="btn-remove" type="button" onclick="removeChild(this)" title="Remove entry">&times;</button>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Name of Child</label>
                                        <input class="form-input" type="text" name="children[]" placeholder="Enter child''s full name">
                                    </div>
                                    <div class="form-group form-group--compact">
                                        <label class="form-label">Age</label>
                                        <input class="form-input" type="number" name="age[]" min="0" max="17" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Assets, Real Properties, and Personal Properties -->
                <section class="form-section">
                    <header class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">5</span>
                            Assets, Real Properties, and Personal Properties
                        </h2>
                    </header>
                    <div class="section-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="asset_description">Description</label>
                                <input class="form-input" type="text" id="asset_description" name="asset_description" placeholder="Describe the asset">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="asset_kind">Kind</label>
                                <input class="form-input" type="text" id="asset_kind" name="asset_kind" placeholder="e.g., Land, Building, Vehicle">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="asset_location">Exact Location</label>
                                <input class="form-input" type="text" id="asset_location" name="asset_location" placeholder="Location of the asset">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="asset_value">Asset Value</label>
                                <input class="form-input" type="text" id="asset_value" name="asset_value" placeholder="Current value">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fair_market_value">Current Fair Market Value</label>
                                <input class="form-input" type="text" id="fair_market_value" name="fair_market_value" placeholder="Market value">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="acquisition_year">Year of Acquisition</label>
                                <input class="form-input" type="number" id="acquisition_year" name="acquisition_year" placeholder="YYYY">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="acquisition_cost">Acquisition Cost</label>
                                <input class="form-input" type="text" id="acquisition_cost" name="acquisition_cost" placeholder="Cost when acquired">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button class="btn-primary" type="submit">
                        Generate SALN Document
                    </button>
                </div>

            </form>
        </main>

        <!-- Footer -->
        <footer class="footer-section">
            <div class="footer-content">
                <p>&copy; 2024 SALN Generator. All rights reserved. This form is for official use only.</p>
                <p class="footer-creator">Andrew B. Malubag | Creator | UI / UX</p>
            </div>
        </footer>
    </div>

    <script src="functions/index.js"></script>
</body>
</html>
