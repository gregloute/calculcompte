
// darkmode //
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

// affiche date picker //
$(document).ready(function(){
    hideEndAT($("#transaction_recurrent"));
    hideEndAT($("#new_transaction_recurrent"));

    function hideEndAT(checkbox){
        if (checkbox.length) {
            console.log(checkbox.is(':checked'))
            if (checkbox.is(':checked')){
                $("#endAt").show();
            }else{
                $("#endAt").hide();
            }
        }

        checkbox.change(function(){
            if ($(this).is(':checked')) {
                $("#endAt").show();
            } else {
                $("#endAt").hide();
            }
        });
    }
});
// date picker //
$(function(){
    $('#new_transaction_endAt').datepicker($.datepicker.regional['fr']);
    $('#transaction_endAt').datepicker($.datepicker.regional['fr']);
});

// auto select logo transaction //

function filter(champRecherche,listeElements,listeOptions){

    let recherche = champRecherche.value.toLowerCase().trim();
    recherche = recherche.split(' ');
    listeElements.innerHTML = '';

    listeOptions.forEach((value, key) => {
        const texteOption = value.toLowerCase();
        for (let i = 0; i < recherche.length; i++){
            if (texteOption.includes(recherche[i])) {
                const option = document.createElement('option');
                option.value = key+1;
                option.textContent = value;
                listeElements.appendChild(option);
            }
        }
    });
    if (listeElements.children.length === 0){
        const option = document.createElement('option');
        option.value = 1;
        option.textContent = "Défaut";
        listeElements.appendChild(option);
    }

}

const champRecherche = document.getElementById('transaction_nom');
const champRecherche2 = document.getElementById('new_transaction_nom');
const listeElements = document.getElementById('transaction_logo');
const listeElements2 = document.getElementById('new_transaction_logo');

if (champRecherche != null && listeElements != null){

    const listeOption = [];
    for (const option of listeElements.children) {
        listeOption.push(option.textContent)
    }


    champRecherche.addEventListener('input', () => {
        filter(champRecherche,listeElements,listeOption)
    })



}
if (champRecherche2 != null && listeElements2 != null){

    const listeOption = [];
    for (const option of listeElements2.children) {
        listeOption.push(option.textContent)
    }
    console.log(listeOption);

    champRecherche2.addEventListener( 'input', () => {
        filter(champRecherche2,listeElements2,listeOption)
    })
}