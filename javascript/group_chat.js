const form = document.querySelector(".typing-area"),
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector(".send-btn"),
chatBox = document.querySelector(".chat-box");

// Prevent form submission
form.onsubmit = (e) => {
    e.preventDefault();
}

// Debug function to check form data
function logFormData(formData) {
    console.log("Form data being sent:");
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
}

function clearInput() {
    inputField.value = "";
    inputField.focus();
}

function sendMessage() {
    const message = inputField.value.trim();
    console.log("Attempting to send message:", message);
    
    if(message !== "") {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/insert_group_chat.php", true);
        
        xhr.onload = () => {
            if(xhr.readyState === XMLHttpRequest.DONE) {
                console.log("Server response:", xhr.response);
                if(xhr.status === 200) {
                    if(xhr.response === "success") {
                        console.log("Message sent successfully");
                        clearInput(); // Clear input after successful send
                        scrollToBottom();
                    } else {
                        console.error("Server error response:", xhr.response);
                    }
                } else {
                    console.error("HTTP error:", xhr.status);
                }
            }
        };

        xhr.onerror = () => {
            console.error("Network error occurred");
        };

        let formData = new FormData(form);
        logFormData(formData);
        xhr.send(formData);
        
        // Clear input immediately after sending
        clearInput();
    } else {
        console.log("Empty message, not sending");
    }
}

// Handle send button click
sendBtn.onclick = (e) => {
    e.preventDefault();
    console.log("Send button clicked");
    sendMessage();
};

// Handle Enter key press
inputField.onkeydown = (e) => {
    if(e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        console.log("Enter key pressed");
        sendMessage();
    }
};

chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

function getGroupMessages() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get_group_chat.php", true);
    xhr.onload = ()=>{
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                if(data) {
                    chatBox.innerHTML = data;
                    if(!chatBox.classList.contains("active")){
                        scrollToBottom();
                    }
                }
            } else {
                console.error("Error fetching messages:", xhr.status);
            }
        }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}

// Initial load of messages
getGroupMessages();

// Update messages every 500ms
setInterval(getGroupMessages, 500);

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Debug info on page load
console.log("Form element:", form);
console.log("Input field:", inputField);
console.log("Send button:", sendBtn);
console.log("Chat box:", chatBox); 