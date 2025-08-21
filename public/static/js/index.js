function getTimeGreeting() {
    const currentHour = new Date().getHours();
    if (currentHour < 12) {
        return "Good morning!";
    } else if (currentHour < 18) {
        return "Good afternoon!";
    } else {
        return "Good evening!";
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const greetingElement = document.getElementById("greeting");
    greetingElement.textContent = getTimeGreeting();
})