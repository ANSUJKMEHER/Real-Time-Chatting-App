const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector(".send-btn"),
chatBox = document.querySelector(".chat-box");


form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}


function sendMessage() {
    if(inputField.value != ""){
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/insert-chat.php", true);
        xhr.onload = ()=>{
            if(xhr.readyState === XMLHttpRequest.DONE){
                if(xhr.status === 200){
                    inputField.value = "";
                    scrollToBottom();
                    sendBtn.classList.remove("active");
                }
            }
        }
        
        let formData = new FormData();
        formData.append('incoming_id', incoming_id);
        formData.append('message', inputField.value);
        xhr.send(formData);
    }
}


sendBtn.addEventListener('click', (e) => {
    e.preventDefault();
    sendMessage();
});


inputField.addEventListener("keydown", (e) => {
    if(e.key === "Enter" && inputField.value != ""){
        e.preventDefault();
        sendMessage();
    }
});

chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

setInterval(() =>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = ()=>{
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                chatBox.innerHTML = data;
                if(!chatBox.classList.contains("active")){
                    scrollToBottom();
                }
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}, 500);

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
}
  