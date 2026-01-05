function toggleDJFields() {
    const typeSelect = document.getElementById('type-selector');
    const djContainer = document.getElementById('dj-selection-container');
    const checkboxes = document.querySelectorAll('.dj-checkbox');
    
    if (typeSelect.value === "WITH DJ/HOST") {
        djContainer.classList.remove('disabled-dj');
        checkboxes.forEach(cb => cb.disabled = false);
    } else {
        djContainer.classList.add('disabled-dj');
        checkboxes.forEach(cb => {
            cb.disabled = true;
            cb.checked = false; // clear selection if switched back
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const typeSelect = document.getElementById('type-selector');
    if (typeSelect) {
        toggleDJFields();
    }
});
