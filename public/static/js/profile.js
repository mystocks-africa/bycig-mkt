document.addEventListener('DOMContentLoaded', function() {
    function handleToggleScreen() {
        // Update URL without reload
        const url = new URL(window.location);
        const tab = url.searchParams.get('tab') ?? "info";

        const userInfoElement = document.getElementById('user-info');
        const userHoldingsElement = document.getElementById('user-holdings');

        const infoTabElement = document.getElementById('info-tab');
        const holdingsTabElement = document.getElementById('holdings-tab');

        // Default view is info so if no tab is specified, show info
        if (!tab || tab === 'info') {
            userInfoElement.style.display = 'block';
            userHoldingsElement.style.display = 'none';

            infoTabElement.classList.add('active');
            holdingsTabElement.classList.remove('active');
        } 
        else if (tab === 'holdings') {
            userHoldingsElement.style.display = 'block';
            userInfoElement.style.display = 'none';

            holdingsTabElement.classList.add('active');
            infoTabElement.classList.remove('active');
        } 
        else {
            throw new Error('Invalid screen type');
        }
    }

    handleToggleScreen();
});

