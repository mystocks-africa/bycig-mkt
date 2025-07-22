const msg_type = window.serverData.msg_type;
const msg = window.serverData.msg;

const textElement = document.getElementById("main-text");
textElement.innerHTML = msg;
textElement.style.color = msg_type === "success" ? "green" : msg_type === "error" ? "red" : "grey";
