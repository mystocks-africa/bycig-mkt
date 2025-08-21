function handleToggleScreen(screen) {
    if (screen === 'info') {
        document.getElementById('user-info').style.display = 'block';
        document.getElementById('user-holdings').style.display = 'none';  
    }

    else if (screen === 'holdings') {
        document.getElementById('user-holdings').style.display = 'block';
        document.getElementById('user-info').style.display = 'none';
    }

    else {
        throw new Error('Invalid screen type');
    }
}