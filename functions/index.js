function addChild() {
    const container = document.getElementById('childrenContainer');

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

    container.appendChild(entry);
}

function removeChild(btn) {
    const container = document.getElementById('childrenContainer');
    if (container.querySelectorAll('.child-entry').length > 1) {
        btn.closest('.child-entry').remove();
    }
}   