function handleToggleScreen(screen) {
    const userInfoElement = document.getElementById('user-info');
    const userHoldingsElement = document.getElementById('user-holdings');

    const infoTabElement = document.getElementById('info-tab');
    const holdingsTabElement = document.getElementById('holdings-tab');

    if (screen === 'info') {
        userInfoElement.style.display = 'block';
        userHoldingsElement.style.display = 'none';

        infoTabElement.classList.add('active');
        holdingsTabElement.classList.remove('active');
    } 
    else if (screen === 'holdings') {
        userHoldingsElement.style.display = 'block';
        userInfoElement.style.display = 'none';

        holdingsTabElement.classList.add('active');
        infoTabElement.classList.remove('active');
    } 
    else {
        throw new Error('Invalid screen type');
    }
}
