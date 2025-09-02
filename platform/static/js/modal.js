function openModal (modalId) {
    if (typeof modalId !== 'string' || modalId.trim() === '') {
        alert("Invalid modal ID.");
        return;
    }

    const modal = document.getElementById(modalId);
    modal.style.display = 'block';
}

function closeModal (modalId) {
    if (typeof modalId !== 'string' || modalId.trim() === '') {
        alert("Invalid modal ID.");
        return;
    }
    
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
}