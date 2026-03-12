<?php

include_once 'saln_download.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALN Generator - Statement of Assets, Liabilities, and Net Worth</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QST1Zc6+1M6x7rZTF0j8q9E0ikM5mTXq0R+0MqZlmCEf5AERHPw/2q6vOqfU9NRM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-GtC6MBxn+F4VYx7/3sZ0iW7KkRX+5c0E4r0Nfw9VwN0XIv5vXhxRIBtql8hB3/fp" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="header-content">
                <h1>Statement of Assets, Liabilities, and Net Worth</h1>
                <p class="subtitle">SALN Form Generator</p>
                <p class="description">Official Declaration Form for Public Officials and Employees</p>
            </div>
        </div>

        <div class="container">
            <form action="saln_download.php" method="POST">

                <!-- Declarant Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">1</span>
                            Declarant Information
                        </h2>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="middle">Middle Initials</label>
                            <input type="text" id="middle" name="middle" placeholder="e.g., M.I.">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="position">Position <span class="required">*</span></label>
                            <input type="text" id="position" name="position" required>
                        </div>
                        <div class="form-group">
                            <label for="agency">Agency / Office <span class="required">*</span></label>
                            <input type="text" id="agency" name="agency" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="address">Office Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" required>
                    </div>
                </div>

                <!-- Spouse Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">2</span>
                            Spouse Information
                        </h2>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="spouse_first_name">First Name</label>
                            <input type="text" id="spouse_first_name" name="spouse_first_name">
                        </div>
                        <div class="form-group">
                            <label for="spouse_middle">Middle Initials</label>
                            <input type="text" id="spouse_middle" name="spouse_middle" placeholder="e.g., M.I.">
                        </div>
                        <div class="form-group">
                            <label for="spouse_last_name">Last Name</label>
                            <input type="text" id="spouse_last_name" name="spouse_last_name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="spouse_position">Position</label>
                            <input type="text" id="spouse_position" name="spouse_position">
                        </div>
                        <div class="form-group">
                            <label for="spouse_agency">Agency / Office</label>
                            <input type="text" id="spouse_agency" name="spouse_agency">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="spouse_address">Office Address</label>
                        <input type="text" id="spouse_address" name="spouse_address">
                    </div>
                </div>

                <!-- Filing Status Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">3</span>
                            Filing Status
                        </h2>
                    </div>

                    <p class="section-note">Spouses who are both public officials or employees may file the SALN jointly or separately. Select the appropriate filing status:</p>

                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="joint" name="filling" value="joint">
                            <label for="joint" class="radio-label">
                                <span class="radio-text">Joint Filing</span>
                            </label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="separate" name="filling" value="separate">
                            <label for="separate" class="radio-label">
                                <span class="radio-text">Separate Filing</span>
                            </label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="na" name="filling" value="na">
                            <label for="na" class="radio-label">
                                <span class="radio-text">Not Applicable</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Children Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="section-number">4</span>
                            Children Information
                        </h2>
                    </div>

                    <p class="section-note">List unmarried children below eighteen (18) years of age living in declarant''s household:</p>

                    <div id="childrenContainer">
                        <div class="child-entry">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Name of Child</label>
                                    <input type="text" name="children[]">
                                </div>
                                <div class="form-group">
                                    <label>Age</label>
                                    <input type="number" name="age[]" min="0" max="18">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="childButton" type="button" class="btn-secondary" onclick="addChild()">
                        <span class="btn-icon">+</span> Add Child
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        <span class="btn-icon"></span> Generate Document
                    </button>
                </div>

            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 SALN Generator. All rights reserved. This form is for official use only.</p>
    </footer>

    <script src="functions/index.js"></script>

</body>

</html>