document.addEventListener('DOMContentLoaded', function() {
    function handleToggleScreen() {
        // Update URL without reload
        const url = new URL(window.location);
        const tab = url.searchParams.get('tab') ?? "info";

        const userInfoElement = document.getElementById('user-info');
        const userHoldingsElement = document.getElementById('user-holdings');
        const deleteUserElement = document.getElementById('delete-user');

        const infoTabElement = document.getElementById('info-tab');
        const holdingsTabElement = document.getElementById('holdings-tab');
        const deleteUserTabElement = document.getElementById('delete-user-tab');

        // Default view is info so if no tab is specified, show info
        if (!tab || tab === 'info') {
            userInfoElement.style.display = 'block';
            userHoldingsElement.style.display = 'none';

            infoTabElement.classList.add('active');
            holdingsTabElement.classList.remove('active');
            deleteUserTabElement.classList.remove('active');
        } 
        else if (tab === 'holdings') {
            userHoldingsElement.style.display = 'block';
            userInfoElement.style.display = 'none';

            holdingsTabElement.classList.add('active');
            infoTabElement.classList.remove('active');
            deleteUserTabElement.classList.remove('active');
        } 
        else if (tab === 'delete-user') {
            deleteUserElement.style.display = 'block';
            userInfoElement.style.display = 'none';
            userHoldingsElement.style.display = 'none';

            deleteUserTabElement.classList.add('active');
            infoTabElement.classList.remove('active');
            holdingsTabElement.classList.remove('active');
        }
        else {
            throw new Error('Invalid screen type');
        }
    }

    handleToggleScreen();
});

function handleUpdateUser (form) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    for (const key in data) {
        const formValue = form.querySelector(`[name="${key}"]`)

        // Value is unchanged, set to null
        if (data[key] === formValue.getAttribute('data-original')) {
            data[key] = null;
        }
    }

    
    fetch('/profile/update-user', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .catch(error => {
        alert('Error updating profile: ' + error.message);
    });
    
}

const updateUserForm = document.querySelector('.update-user-form');
updateUserForm.addEventListener('submit', function(event) {
    event.preventDefault();
    handleUpdateUser(updateUserForm); 
})

function handleDeleteUser() {
    if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
        fetch('/profile/delete-user', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .catch(error => {
            alert('Error deleting account: ' + error.message);
        });
    }
}
