document.addEventListener('DOMContentLoaded', function() {
    function handleToggleScreen() {
        const url = new URL(window.location);
        const tab = url.searchParams.get('tab') ?? "info";

        const userInfoElement = document.getElementById('user-info');
        const userHoldingsElement = document.getElementById('user-holdings');
        const deleteUserElement = document.getElementById('delete-user');

        const infoTabElement = document.getElementById('info-tab');
        const holdingsTabElement = document.getElementById('holdings-tab');
        const deleteUserTabElement = document.getElementById('delete-user-tab');

        if (userInfoElement) userInfoElement.style.display = 'none';
        if (userHoldingsElement) userHoldingsElement.style.display = 'none';
        if (deleteUserElement) deleteUserElement.style.display = 'none';

        if (infoTabElement) infoTabElement.classList.remove('active');
        if (holdingsTabElement) holdingsTabElement.classList.remove('active');
        if (deleteUserTabElement) deleteUserTabElement.classList.remove('active');

        if (!tab || tab === 'info') {
            if (userInfoElement) {
                userInfoElement.style.display = 'block';
                infoTabElement?.classList.add('active');
            }
        } else if (tab === 'holdings') {
            if (userHoldingsElement) {
                userHoldingsElement.style.display = 'block';
                holdingsTabElement?.classList.add('active');
            }
        } else if (tab === 'delete-user') {
            if (deleteUserElement) {
                deleteUserElement.style.display = 'block';
                deleteUserTabElement?.classList.add('active');
            }
        } else {
            console.error('Invalid screen type:', tab);
            if (userInfoElement) {
                userInfoElement.style.display = 'block';
                infoTabElement?.classList.add('active');
            }
        }
    }

    handleToggleScreen();
});

function handleUpdateUser(form) {
    const formData = new FormData(form);
    const modifiedData = new FormData();
    let hasChanges = false;
    
    for (const [key, value] of formData.entries()) {
        const formField = form.querySelector(`[name="${key}"]`);
        const originalValue = formField?.getAttribute('data-original') || '';
        if (value !== originalValue) {
            modifiedData.append(key, value);
            hasChanges = true;
        }
    }
        
    if (!hasChanges) {
        alert('No changes detected.');
        return;
    }
    
    fetch('/profile/update-user', {
        method: 'POST',
        body: modifiedData
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status} - ${text}`);
            });
        }
    })
    .catch(error => {
        console.error('Error updating profile:', error);
        alert('Error updating profile: ' + error.message);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const updateUserForm = document.querySelector('.update-user-form');
    if (updateUserForm) {
        updateUserForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleUpdateUser(updateUserForm); 
        });
    }
});

function handleDeleteUser() {
    if (!confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
        return;
    }

    fetch('/profile/delete-user', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status} - ${text}`);
            });
        }
    })
    .catch(error => {
        console.error('Error deleting account:', error);
        alert('Error deleting account: ' + error.message);
    });
}
