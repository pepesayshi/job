require('./bootstrap');

function dropdown(value){
    document.getElementById('dropdownMenuButton1').innerHTML=value;
    document.getElementById('jobstatus').value=value;
};

function note(id){
    document.getElementById('singlenotelabel-'+id).classList.add("d-none");
    document.getElementById('singlenotetext-'+id).classList.remove("d-none");
    document.getElementById('savenote-'+id).classList.remove("d-none");
    document.getElementById('editnote-'+id).classList.add("d-none");
};