let darkToggle = document.querySelector('#darkmodeSwitch');

let save = localStorage.getItem("theme");

if (save === "dark"){
    document.body.classList.toggle('dark-mode');
    darkToggle.checked = true;
}

darkToggle.addEventListener('change', ()=> {
    document.body.classList.toggle('dark-mode');

    if (localStorage.getItem("theme") === "dark"){
        localStorage.removeItem("theme");
    }else{
        localStorage.setItem("theme","dark");
    }
})