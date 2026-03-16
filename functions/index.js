// ==========================================
// DataTable AJAX CRUD Controller
// ==========================================
const API_ENDPOINT = 'functions/datatable_api.php';
const dataTableState = {};
const DRAFT_TOKEN_KEY = 'saln_draft_token';

function generateDraftToken() {
    if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
        const bytes = new Uint8Array(16);
        window.crypto.getRandomValues(bytes);
        return Array.from(bytes, (value) => value.toString(16).padStart(2, '0')).join('');
    }

    return `${Date.now().toString(16)}${Math.random().toString(16).slice(2, 18)}`;
}

function getDraftToken() {
    const existing = window.localStorage.getItem(DRAFT_TOKEN_KEY);
    if (existing && /^[A-Za-z0-9_-]{16,64}$/.test(existing)) {
        return existing;
    }

    const token = generateDraftToken();
    window.localStorage.setItem(DRAFT_TOKEN_KEY, token);
    return token;
}

function ensureDraftTokenField() {
    const form = document.getElementById('salnForm');
    if (!form) {
        return;
    }

    const token = getDraftToken();
    let input = form.querySelector('input[name="draft_token"]');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'draft_token';
        form.appendChild(input);
    }

    input.value = token;
}

const entryTableConfigs = {
    annexAChildren: {
        key: 'annexAChildren',
        tableId: 'annexAChildrenTable',
        containerId: 'childrenContainer',
        entrySelector: '.child-entry',
        addHandlerName: 'addChild',
        fields: [
            { key: 'children', selector: 'input[name="children[]"]', name: 'children[]', label: 'Name of Child' },
            { key: 'age', selector: 'input[name="age[]"]', name: 'age[]', label: 'Age' }
        ]
    },
    annexAAssets: {
        key: 'annexAAssets',
        tableId: 'annexAAssetsTable',
        containerId: 'assetContainer',
        entrySelector: '.asset-entry',
        addHandlerName: 'addAsset',
        fields: [
            { key: 'asset_description', selector: 'input[name="asset_description[]"]', name: 'asset_description[]', label: 'Description' },
            { key: 'asset_kind', selector: 'input[name="asset_kind[]"]', name: 'asset_kind[]', label: 'Kind' },
            { key: 'asset_location', selector: 'input[name="asset_location[]"]', name: 'asset_location[]', label: 'Exact Location' },
            { key: 'asset_value', selector: 'input[name="asset_value[]"]', name: 'asset_value[]', label: 'Asset Value' },
            { key: 'fair_market_value', selector: 'input[name="fair_market_value[]"]', name: 'fair_market_value[]', label: 'Fair Market Value' },
            { key: 'acquisition_year', selector: 'input[name="acquisition_year[]"]', name: 'acquisition_year[]', label: 'Year Acquired' },
            { key: 'acquisition_mode', selector: 'input[name="acquisition_mode[]"]', name: 'acquisition_mode[]', label: 'Acquisition Mode' },
            { key: 'acquisition_cost', selector: 'input[name="acquisition_cost[]"]', name: 'acquisition_cost[]', label: 'Acquisition Cost' }
        ]
    },
    annexBRealProperty: {
        key: 'annexBRealProperty',
        tableId: 'annexBRealPropertyTable',
        containerId: 'annexBRealPropertyContainer',
        entrySelector: '.annexb-real-entry',
        addHandlerName: 'addAnnexBRealProperty',
        fields: [
            { key: 'real_property_description', selector: 'input[name="real_property_description[]"]', name: 'real_property_description[]', label: 'Description' },
            { key: 'real_property_kind', selector: 'input[name="real_property_kind[]"]', name: 'real_property_kind[]', label: 'Kind' },
            { key: 'real_property_location', selector: 'input[name="real_property_location[]"]', name: 'real_property_location[]', label: 'Exact Location' },
            { key: 'real_property_assessed_value', selector: 'input[name="real_property_assessed_value[]"]', name: 'real_property_assessed_value[]', label: 'Assessed Value' },
            { key: 'real_property_fair_market_value', selector: 'input[name="real_property_fair_market_value[]"]', name: 'real_property_fair_market_value[]', label: 'Fair Market Value' },
            { key: 'real_property_acquisition_year', selector: 'input[name="real_property_acquisition_year[]"]', name: 'real_property_acquisition_year[]', label: 'Year Acquired' },
            { key: 'real_property_acquisition_mode', selector: 'input[name="real_property_acquisition_mode[]"]', name: 'real_property_acquisition_mode[]', label: 'Acquisition Mode' },
            { key: 'real_property_acquisition_cost', selector: 'input[name="real_property_acquisition_cost[]"]', name: 'real_property_acquisition_cost[]', label: 'Acquisition Cost' }
        ]
    },
    annexBPersonalProperty: {
        key: 'annexBPersonalProperty',
        tableId: 'annexBPersonalPropertyTable',
        containerId: 'annexBPersonalPropertyContainer',
        entrySelector: '.annexb-personal-entry',
        addHandlerName: 'addAnnexBPersonalProperty',
        fields: [
            { key: 'personal_property_description', selector: 'input[name="personal_property_description[]"]', name: 'personal_property_description[]', label: 'Description' },
            { key: 'personal_property_acquisition_year', selector: 'input[name="personal_property_acquisition_year[]"]', name: 'personal_property_acquisition_year[]', label: 'Year Acquired' },
            { key: 'personal_property_amount', selector: 'input[name="personal_property_amount[]"]', name: 'personal_property_amount[]', label: 'Acquisition Cost / Amount' }
        ]
    },
    annexBLiability: {
        key: 'annexBLiability',
        tableId: 'annexBLiabilityTable',
        containerId: 'annexBLiabilityContainer',
        entrySelector: '.annexb-liability-entry',
        addHandlerName: 'addAnnexBLiability',
        fields: [
            { key: 'liability_nature', selector: 'input[name="liability_nature[]"]', name: 'liability_nature[]', label: 'Nature' },
            { key: 'liability_creditor', selector: 'input[name="liability_creditor[]"]', name: 'liability_creditor[]', label: 'Name of Creditor' },
            { key: 'liability_balance', selector: 'input[name="liability_balance[]"]', name: 'liability_balance[]', label: 'Outstanding Balance' }
        ]
    },
    annexBBusinessInterest: {
        key: 'annexBBusinessInterest',
        tableId: 'annexBBusinessTable',
        containerId: 'annexBBusinessContainer',
        entrySelector: '.annexb-business-entry',
        addHandlerName: 'addAnnexBBusinessInterest',
        fields: [
            { key: 'business_entity_name', selector: 'input[name="business_entity_name[]"]', name: 'business_entity_name[]', label: 'Entity / Business Enterprise' },
            { key: 'business_address', selector: 'input[name="business_address[]"]', name: 'business_address[]', label: 'Business Address' },
            { key: 'business_interest_nature', selector: 'input[name="business_interest_nature[]"]', name: 'business_interest_nature[]', label: 'Nature of Interest / Connection' },
            { key: 'business_interest_acquisition_date', selector: 'input[name="business_interest_acquisition_date[]"]', name: 'business_interest_acquisition_date[]', label: 'Acquisition Date' }
        ]
    },
    annexCChildren: {
        key: 'annexCChildren',
        tableId: 'annexCChildrenTable',
        containerId: 'annexCChildContainer',
        entrySelector: '.annexc-child-entry',
        addHandlerName: 'addAnnexCChild',
        fields: [
            { key: 'annexc_child_name', selector: 'input[name="annexc_child_name[]"]', name: 'annexc_child_name[]', label: 'Name of Child' },
            { key: 'annexc_child_age', selector: 'input[name="annexc_child_age[]"]', name: 'annexc_child_age[]', label: 'Age' },
            { key: 'annexc_child_relationship', selector: 'input[name="annexc_child_relationship[]"]', name: 'annexc_child_relationship[]', label: 'Relationship' }
        ]
    },
    annexCRealProperty: {
        key: 'annexCRealProperty',
        tableId: 'annexCRealPropertyTable',
        containerId: 'annexCRealPropertyContainer',
        entrySelector: '.annexc-real-entry',
        addHandlerName: 'addAnnexCRealProperty',
        fields: [
            { key: 'annexc_real_description', selector: 'input[name="annexc_real_description[]"]', name: 'annexc_real_description[]', label: 'Description' },
            { key: 'annexc_real_kind', selector: 'input[name="annexc_real_kind[]"]', name: 'annexc_real_kind[]', label: 'Kind' },
            { key: 'annexc_real_location', selector: 'input[name="annexc_real_location[]"]', name: 'annexc_real_location[]', label: 'Exact Location' },
            { key: 'annexc_real_assessed_value', selector: 'input[name="annexc_real_assessed_value[]"]', name: 'annexc_real_assessed_value[]', label: 'Assessed Value' },
            { key: 'annexc_real_fair_market_value', selector: 'input[name="annexc_real_fair_market_value[]"]', name: 'annexc_real_fair_market_value[]', label: 'Fair Market Value' },
            { key: 'annexc_real_acquisition_year', selector: 'input[name="annexc_real_acquisition_year[]"]', name: 'annexc_real_acquisition_year[]', label: 'Year Acquired' },
            { key: 'annexc_real_acquisition_mode', selector: 'input[name="annexc_real_acquisition_mode[]"]', name: 'annexc_real_acquisition_mode[]', label: 'Acquisition Mode' },
            { key: 'annexc_real_acquisition_cost', selector: 'input[name="annexc_real_acquisition_cost[]"]', name: 'annexc_real_acquisition_cost[]', label: 'Acquisition Cost' }
        ]
    },
    annexCPersonalProperty: {
        key: 'annexCPersonalProperty',
        tableId: 'annexCPersonalPropertyTable',
        containerId: 'annexCPersonalPropertyContainer',
        entrySelector: '.annexc-personal-entry',
        addHandlerName: 'addAnnexCPersonalProperty',
        fields: [
            { key: 'annexc_personal_description', selector: 'input[name="annexc_personal_description[]"]', name: 'annexc_personal_description[]', label: 'Description' },
            { key: 'annexc_personal_acquisition_year', selector: 'input[name="annexc_personal_acquisition_year[]"]', name: 'annexc_personal_acquisition_year[]', label: 'Year Acquired' },
            { key: 'annexc_personal_amount', selector: 'input[name="annexc_personal_amount[]"]', name: 'annexc_personal_amount[]', label: 'Acquisition Cost / Amount' }
        ]
    },
    annexCBusinessInterest: {
        key: 'annexCBusinessInterest',
        tableId: 'annexCBusinessTable',
        containerId: 'annexCBusinessContainer',
        entrySelector: '.annexc-business-entry',
        addHandlerName: 'addAnnexCBusinessInterest',
        fields: [
            { key: 'annexc_business_entity_name', selector: 'input[name="annexc_business_entity_name[]"]', name: 'annexc_business_entity_name[]', label: 'Entity / Business Enterprise' },
            { key: 'annexc_business_address', selector: 'input[name="annexc_business_address[]"]', name: 'annexc_business_address[]', label: 'Business Address' },
            { key: 'annexc_business_nature', selector: 'input[name="annexc_business_nature[]"]', name: 'annexc_business_nature[]', label: 'Nature of Interest / Connection' },
            { key: 'annexc_business_acquisition_date', selector: 'input[name="annexc_business_acquisition_date[]"]', name: 'annexc_business_acquisition_date[]', label: 'Acquisition Date' }
        ]
    }
};

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getConfig(configKey) {
    return entryTableConfigs[configKey] || null;
}

function getEntryElement(config) {
    const container = document.getElementById(config.containerId);
    if (!container) {
        return null;
    }

    return container.querySelector(config.entrySelector);
}

function getAddButton(config) {
    return document.querySelector(`button[onclick=\"${config.addHandlerName}()\"]`);
}

function ensureTableSkeleton(config) {
    const container = document.getElementById(config.containerId);
    if (!container) {
        return null;
    }

    let table = document.getElementById(config.tableId);
    if (!table) {
        const wrapper = document.createElement('div');
        wrapper.className = 'data-table-wrapper';

        table = document.createElement('table');
        table.id = config.tableId;
        table.className = 'display data-entry-table';

        const thead = document.createElement('thead');
        const tr = document.createElement('tr');

        config.fields.forEach((field) => {
            const th = document.createElement('th');
            th.textContent = field.label;
            tr.appendChild(th);
        });

        const actionTh = document.createElement('th');
        actionTh.textContent = 'Action';
        tr.appendChild(actionTh);

        thead.appendChild(tr);
        table.appendChild(thead);
        table.appendChild(document.createElement('tbody'));

        wrapper.appendChild(table);
        container.insertAdjacentElement('afterend', wrapper);
    }

    return table;
}

function ensureState(configKey) {
    const config = getConfig(configKey);
    if (!config) {
        return null;
    }

    const tableElement = ensureTableSkeleton(config);
    if (!tableElement) {
        return null;
    }

    if (!dataTableState[configKey]) {
        const button = getAddButton(config);
        const addLabel = button ? button.textContent.trim() : 'Add';

        dataTableState[configKey] = {
            dt: typeof DataTable === 'function'
                ? new DataTable(`#${config.tableId}`, {
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false,
                    autoWidth: false,
                    scrollX: true,
                    language: { emptyTable: 'No records added yet.' }
                })
                : null,
            rows: [],
            editingId: null,
            addLabel
        };
    }

    return dataTableState[configKey];
}

function getHiddenInputContainer(config) {
    const form = document.getElementById('salnForm');
    if (!form) {
        return null;
    }

    const hiddenContainerId = `${config.tableId}HiddenInputs`;
    let hiddenContainer = document.getElementById(hiddenContainerId);
    if (!hiddenContainer) {
        hiddenContainer = document.createElement('div');
        hiddenContainer.id = hiddenContainerId;
        hiddenContainer.className = 'table-hidden-inputs';
        form.appendChild(hiddenContainer);
    }

    return hiddenContainer;
}

function syncHiddenInputs(configKey) {
    const config = getConfig(configKey);
    const state = dataTableState[configKey];
    if (!config || !state) {
        return;
    }

    const hiddenContainer = getHiddenInputContainer(config);
    if (!hiddenContainer) {
        return;
    }

    hiddenContainer.innerHTML = '';

    state.rows.forEach((row) => {
        config.fields.forEach((field) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = field.name;
            input.value = row[field.key] || '';
            hiddenContainer.appendChild(input);
        });
    });
}

function clearEntryInputs(configKey) {
    const config = getConfig(configKey);
    const entry = config ? getEntryElement(config) : null;

    if (!entry) {
        return;
    }

    config.fields.forEach((field) => {
        const input = entry.querySelector(field.selector);
        if (!input) {
            return;
        }

        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
}

function readEntryValues(configKey) {
    const config = getConfig(configKey);
    const entry = config ? getEntryElement(config) : null;

    if (!config || !entry) {
        return null;
    }

    const values = {};
    config.fields.forEach((field) => {
        const input = entry.querySelector(field.selector);
        values[field.key] = input ? String(input.value).trim() : '';
    });

    return values;
}

function writeEntryValues(configKey, row) {
    const config = getConfig(configKey);
    const entry = config ? getEntryElement(config) : null;

    if (!config || !entry || !row) {
        return;
    }

    config.fields.forEach((field) => {
        const input = entry.querySelector(field.selector);
        if (input) {
            input.value = row[field.key] || '';
        }
    });
}

function normalizeStateRow(configKey, rawRow = {}, fallbackId = '') {
    const config = getConfig(configKey);
    if (!config) {
        return null;
    }

    const normalized = {
        id: String((rawRow && rawRow.id) || fallbackId || '')
    };

    config.fields.forEach((field) => {
        const rawValue = rawRow && Object.prototype.hasOwnProperty.call(rawRow, field.key)
            ? rawRow[field.key]
            : '';
        normalized[field.key] = rawValue == null ? '' : String(rawValue).trim();
    });

    return normalized;
}

function setAddButtonMode(configKey, isEditMode) {
    const config = getConfig(configKey);
    const state = dataTableState[configKey];
    const button = config ? getAddButton(config) : null;

    if (!config || !state || !button) {
        return;
    }

    button.textContent = isEditMode ? 'Update Entry' : state.addLabel;
}

function setEditing(configKey, rowId) {
    const state = dataTableState[configKey];
    if (!state) {
        return;
    }

    state.editingId = rowId || null;
    setAddButtonMode(configKey, Boolean(rowId));
}

function buildActionButtons(configKey, rowId) {
    return `
        <div class="btn-table-actions">
            <button type="button" class="btn-table-edit" data-config="${escapeHtml(configKey)}" data-id="${escapeHtml(rowId)}">Edit</button>
            <button type="button" class="btn-table-remove" data-config="${escapeHtml(configKey)}" data-id="${escapeHtml(rowId)}">Delete</button>
        </div>
    `;
}

function renderTable(configKey) {
    const config = getConfig(configKey);
    const state = dataTableState[configKey];
    if (!config || !state || !state.dt) {
        return;
    }

    const rows = state.rows.map((row) => {
        const cells = config.fields.map((field) => escapeHtml(row[field.key] || '-'));
        cells.push(buildActionButtons(configKey, row.id));
        return cells;
    });

    state.dt.clear();
    if (rows.length > 0) {
        state.dt.rows.add(rows);
    }
    state.dt.draw(false);

    syncHiddenInputs(configKey);
}

async function apiRequest(action, type, payload = {}) {
    const requestPayload = {
        action,
        type,
        draft_token: getDraftToken(),
        ...payload
    };

    const response = await fetch(API_ENDPOINT, {
        method: 'POST',
        credentials: 'same-origin',
        cache: 'no-store',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestPayload)
    });

    if (!response.ok) {
        throw new Error(`Request failed (${response.status})`);
    }

    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Request failed.');
    }

    return result;
}

function notifyAjaxError(error, fallbackMessage) {
    const message = (error && error.message) ? error.message : fallbackMessage;
    console.error(error);
    alert(message);
}

async function loadRows(configKey) {
    const state = ensureState(configKey);
    const config = getConfig(configKey);

    if (!state || !config) {
        return;
    }

    try {
        const result = await apiRequest('list', configKey);
        const rows = Array.isArray(result.data) ? result.data : [];
        state.rows = rows
            .map((row) => normalizeStateRow(configKey, row))
            .filter((row) => row && row.id !== '');
    } catch (error) {
        state.rows = [];
        notifyAjaxError(error, 'Unable to load table entries.');
    }

    renderTable(configKey);
}

async function saveEntry(configKey) {
    const state = ensureState(configKey);
    const config = getConfig(configKey);
    if (!state || !config) {
        return;
    }

    const values = readEntryValues(configKey);
    if (!values) {
        return;
    }

    const hasData = Object.values(values).some((value) => value !== '');
    if (!hasData) {
        return;
    }

    try {
        const isEditing = Boolean(state.editingId);
        const editingId = String(state.editingId || '');

        if (state.editingId) {
            const result = await apiRequest('update', configKey, { id: editingId, data: values });
            const normalized = normalizeStateRow(configKey, result.data || values, editingId);

            if (normalized) {
                state.rows = state.rows.map((row) => (String(row.id) === editingId ? normalized : row));
            }
        } else {
            const result = await apiRequest('create', configKey, { data: values });
            const normalized = normalizeStateRow(configKey, result.data || values);

            if (normalized && normalized.id !== '') {
                state.rows.push(normalized);
            }
        }

        if (!isEditing && state.rows.length === 0) {
            await loadRows(configKey);
        } else {
            renderTable(configKey);
        }
        clearEntryInputs(configKey);
        setEditing(configKey, null);
    } catch (error) {
        notifyAjaxError(error, 'Unable to save this entry.');
    }
}

function handleEdit(configKey, rowId) {
    const state = dataTableState[configKey];
    if (!state) {
        return;
    }

    const targetId = String(rowId);
    const row = state.rows.find((item) => String(item.id) === targetId);
    if (!row) {
        return;
    }

    writeEntryValues(configKey, row);
    setEditing(configKey, targetId);
}

async function handleDelete(configKey, rowId) {
    try {
        const state = dataTableState[configKey];
        const targetId = String(rowId);

        await apiRequest('delete', configKey, { id: targetId });

        if (state) {
            state.rows = state.rows.filter((row) => String(row.id) !== targetId);
        }

        if (state && String(state.editingId || '') === targetId) {
            clearEntryInputs(configKey);
            setEditing(configKey, null);
        }

        renderTable(configKey);
    } catch (error) {
        notifyAjaxError(error, 'Unable to delete this entry.');
    }
}

async function clearRows(configKey) {
    const state = ensureState(configKey);
    if (!state) {
        return;
    }

    try {
        await apiRequest('clear', configKey);
        state.rows = [];
        renderTable(configKey);
        clearEntryInputs(configKey);
        setEditing(configKey, null);
    } catch (error) {
        notifyAjaxError(error, 'Unable to clear entries.');
    }
}

function getActiveConfigKeys() {
    return Object.keys(entryTableConfigs).filter((configKey) => {
        const config = getConfig(configKey);
        return config && document.getElementById(config.containerId);
    });
}

function clearAllEntryTables() {
    const activeConfigKeys = getActiveConfigKeys();
    const jobs = activeConfigKeys.map((configKey) => clearRows(configKey));
    return Promise.all(jobs);
}

function syncAnnexSelector() {
    const annexType = document.getElementById('annex_type');

    if (!annexType) {
        return;
    }

    const path = window.location.pathname.toLowerCase();
    if (path.endsWith('/annexb.php')) {
        annexType.value = 'annexB.php';
        return;
    }

    if (path.endsWith('/annexc.php')) {
        annexType.value = 'annexC.php';
        return;
    }

    annexType.value = 'index.php';
}

function navigateToAnnexPage() {
    const annexType = document.getElementById('annex_type');
    if (!annexType || !annexType.value) {
        return;
    }

    const targetPage = annexType.value;
    const currentPage = window.location.pathname.split('/').pop().toLowerCase() || 'index.php';

    if (currentPage !== targetPage.toLowerCase()) {
        window.location.href = targetPage;
    }
}

async function initializeTables() {
    const activeConfigKeys = getActiveConfigKeys();
    for (const configKey of activeConfigKeys) {
        ensureState(configKey);
        await loadRows(configKey);
    }
}

function wireTableActions() {
    document.addEventListener('click', async (event) => {
        const editButton = event.target.closest('.btn-table-edit');
        if (editButton) {
            const configKey = editButton.getAttribute('data-config');
            const rowId = editButton.getAttribute('data-id');
            if (configKey && rowId) {
                handleEdit(configKey, rowId);
            }
            return;
        }

        const deleteButton = event.target.closest('.btn-table-remove[data-config][data-id]');
        if (deleteButton) {
            const configKey = deleteButton.getAttribute('data-config');
            const rowId = deleteButton.getAttribute('data-id');
            if (configKey && rowId) {
                await handleDelete(configKey, rowId);
            }
        }
    });
}

function wireFormSubmit() {
    const form = document.getElementById('salnForm');
    if (!form) {
        return;
    }

    form.addEventListener('submit', () => {
        ensureDraftTokenField();
        const activeConfigKeys = getActiveConfigKeys();
        activeConfigKeys.forEach((configKey) => {
            syncHiddenInputs(configKey);
            clearEntryInputs(configKey);
        });
    });
}

// ==========================================
// Annex A Functions (index.php)
// ==========================================
function addChild() {
    return saveEntry('annexAChildren');
}

function removeChildEntry() {
    clearEntryInputs('annexAChildren');
    setEditing('annexAChildren', null);
}

function addAsset() {
    return saveEntry('annexAAssets');
}

function removeAsset() {
    clearEntryInputs('annexAAssets');
    setEditing('annexAAssets', null);
}

// ==========================================
// Annex B Functions (annexB.php)
// ==========================================
function addAnnexBRealProperty() {
    return saveEntry('annexBRealProperty');
}

function removeAnnexBRealProperty() {
    clearEntryInputs('annexBRealProperty');
    setEditing('annexBRealProperty', null);
}

function addAnnexBPersonalProperty() {
    return saveEntry('annexBPersonalProperty');
}

function removeAnnexBPersonalProperty() {
    clearEntryInputs('annexBPersonalProperty');
    setEditing('annexBPersonalProperty', null);
}

function addAnnexBLiability() {
    return saveEntry('annexBLiability');
}

function removeAnnexBLiability() {
    clearEntryInputs('annexBLiability');
    setEditing('annexBLiability', null);
}

function addAnnexBBusinessInterest() {
    return saveEntry('annexBBusinessInterest');
}

function removeAnnexBBusinessInterest() {
    clearEntryInputs('annexBBusinessInterest');
    setEditing('annexBBusinessInterest', null);
}

// ==========================================
// Annex C Functions (annexC.php)
// ==========================================
function addAnnexCChild() {
    return saveEntry('annexCChildren');
}

function removeAnnexCChild() {
    clearEntryInputs('annexCChildren');
    setEditing('annexCChildren', null);
}

function addAnnexCRealProperty() {
    return saveEntry('annexCRealProperty');
}

function removeAnnexCRealProperty() {
    clearEntryInputs('annexCRealProperty');
    setEditing('annexCRealProperty', null);
}

function addAnnexCPersonalProperty() {
    return saveEntry('annexCPersonalProperty');
}

function removeAnnexCPersonalProperty() {
    clearEntryInputs('annexCPersonalProperty');
    setEditing('annexCPersonalProperty', null);
}

function addAnnexCBusinessInterest() {
    return saveEntry('annexCBusinessInterest');
}

function removeAnnexCBusinessInterest() {
    clearEntryInputs('annexCBusinessInterest');
    setEditing('annexCBusinessInterest', null);
}

// ==========================================
// Shared Form Actions and Annex Navigation
// ==========================================
async function clearForm() {
    const form = document.getElementById('salnForm');
    if (!form) {
        return;
    }

    form.reset();
    ensureDraftTokenField();
    await clearAllEntryTables();
}

window.addChild = addChild;
window.removeChildEntry = removeChildEntry;
window.addAsset = addAsset;
window.removeAsset = removeAsset;
window.addAnnexBRealProperty = addAnnexBRealProperty;
window.removeAnnexBRealProperty = removeAnnexBRealProperty;
window.addAnnexBPersonalProperty = addAnnexBPersonalProperty;
window.removeAnnexBPersonalProperty = removeAnnexBPersonalProperty;
window.addAnnexBLiability = addAnnexBLiability;
window.removeAnnexBLiability = removeAnnexBLiability;
window.addAnnexBBusinessInterest = addAnnexBBusinessInterest;
window.removeAnnexBBusinessInterest = removeAnnexBBusinessInterest;
window.addAnnexCChild = addAnnexCChild;
window.removeAnnexCChild = removeAnnexCChild;
window.addAnnexCRealProperty = addAnnexCRealProperty;
window.removeAnnexCRealProperty = removeAnnexCRealProperty;
window.addAnnexCPersonalProperty = addAnnexCPersonalProperty;
window.removeAnnexCPersonalProperty = removeAnnexCPersonalProperty;
window.addAnnexCBusinessInterest = addAnnexCBusinessInterest;
window.removeAnnexCBusinessInterest = removeAnnexCBusinessInterest;
window.clearForm = clearForm;
window.navigateToAnnexPage = navigateToAnnexPage;

document.addEventListener('DOMContentLoaded', async () => {
    ensureDraftTokenField();
    syncAnnexSelector();
    wireTableActions();
    wireFormSubmit();
    await initializeTables();
});
