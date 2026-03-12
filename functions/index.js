function addChild() {
    const container = document.getElementById("childrenContainer");

    const entryDiv = document.createElement('div');
    entryDiv.classList.add('child-entry');

    const rowDiv = document.createElement('div');
    rowDiv.classList.add('form-row');

    const childGroupDiv = document.createElement('div');
    childGroupDiv.classList.add('form-group');
    childGroupDiv.innerHTML = `
        <label>Name of Child</label>
        <input type="text" name="children[]">
    `;

    const ageGroupDiv = document.createElement('div');
    ageGroupDiv.classList.add('form-group');
    ageGroupDiv.innerHTML = `
        <label>Age</label>
        <input type="number" name="age[]" min="0" max="18">
    `;

    rowDiv.appendChild(childGroupDiv);
    rowDiv.appendChild(ageGroupDiv);
    entryDiv.appendChild(rowDiv);
    container.appendChild(entryDiv);
}   