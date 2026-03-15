function createChildEntry() {
    const entry = document.createElement('div');
    entry.classList.add('child-entry');
    entry.innerHTML = `
        <button type="button" class="btn-remove" onclick="removeChild(this)" title="Remove entry">&times;</button>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Name of Child</label>
                <input class="form-input" type="text" name="children[]" placeholder="Enter child's full name">
            </div>
            <div class="form-group form-group--compact">
                <label class="form-label">Age</label>
                <input class="form-input" type="number" name="age[]" min="0" max="17" placeholder="0">
            </div>
        </div>
    `;

    return entry;
}

function createAssetEntry() {
    const entry = document.createElement('div');
    entry.classList.add('child-entry', 'asset-entry');
    entry.innerHTML = `
        <button type="button" class="btn-remove" onclick="removeAsset(this)" title="Remove entry">&times;</button>
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
    `;

    return entry;
}

function addChild() {
    const container = document.getElementById('childrenContainer');
    if (!container) {
        return;
    }

    const entry = createChildEntry();

    container.appendChild(entry);
}

function removeChild(btn) {
    const container = document.getElementById('childrenContainer');
    if (container && container.querySelectorAll('.child-entry').length > 1) {
        btn.closest('.child-entry').remove();
    }
}

function addAsset() {
    const container = document.getElementById('assetContainer');
    if (!container) {
        return;
    }

    const entry = createAssetEntry();
    container.appendChild(entry);
}

function removeAsset(btn) {
    const container = document.getElementById('assetContainer');
    if (container && container.querySelectorAll('.asset-entry').length > 1) {
        btn.closest('.asset-entry').remove();
    }
}

function clearForm() {
    const form = document.getElementById("salnForm");
    const childrenContainer = document.getElementById("childrenContainer");
    const assetContainer = document.getElementById("assetContainer");

    if (!form) return;

    // Reset the form normally
    form.reset();

    // Force clear all inputs
    const inputs = form.querySelectorAll("input");
    inputs.forEach(input => {
        if (input.type === "radio" || input.type === "checkbox") {
            input.checked = false;
        } else {
            input.value = "";
        }
    });

    // Reset children list
    if (childrenContainer) {
        childrenContainer.innerHTML = "";
        childrenContainer.appendChild(createChildEntry());
    }

    if (assetContainer) {
        assetContainer.innerHTML = "";
        assetContainer.appendChild(createAssetEntry());
    }
}

window.addChild = addChild;
window.removeChild = removeChild;
window.addAsset = addAsset;
window.removeAsset = removeAsset;
window.clearForm = clearForm;