function handleToggleScreen(screen) {
    const userInfoElement = document.getElementById('user-info');
    const userHoldingsElement = document.getElementById('user-holdings');

    if (screen === 'info') {
        userInfoElement.style.display = 'block';
        userHoldingsElement.getElementById('user-holdings').style.display = 'none';  
    }

    else if (screen === 'holdings') {
        userHoldingsElement.style.display = 'block';
        userInfoElement.style.display = 'none';
    }

    else {
        throw new Error('Invalid screen type');
    }
}