const searchBar = document.querySelector(".search input"),
searchText = document.querySelector(".search .text"),
usersList = document.querySelector(".users-list");

searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
  }
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          usersList.innerHTML = data;
        }
    }
  }
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm);
}

setInterval(() =>{
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "php/users.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          if(!searchBar.classList.contains("active")){
            usersList.innerHTML = data;
          }
        }
    }
  }
  xhr.send();
}, 500);


usersList.onclick = (e) => {
  const userElement = e.target.closest('.user');
  if (userElement) {
    const userId = userElement.getAttribute('data-id');
    if (userId) {
      window.location.href = `chat.php?user_id=${userId}`;
    }
  }
};


const toggleUsers = () => {
  const usersSection = document.querySelector('.users');
  usersSection.classList.toggle('active');
};


const addMobileToggle = () => {
  const chatHeader = document.querySelector('.chat-area header');
  const toggleBtn = document.createElement('div');
  toggleBtn.className = 'mobile-toggle';
  toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
  toggleBtn.onclick = toggleUsers;
  chatHeader.insertBefore(toggleBtn, chatHeader.firstChild);
};


if (window.innerWidth <= 768) {
  addMobileToggle();
}

