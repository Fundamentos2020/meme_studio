const botonRegistro = document.getElementById('boton-registro');
botonRegistro.addEventListener('click', registrarUsuario);

function registrarUsuario(e) {
    e.preventDefault();

    var xhr = new XMLHttpRequest();

    xhr.open("POST", API + "usuarios", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        data = responseText.data;
        if(this.status === 201) {
            alert(responseText.messages);
            window.location.href = "./inicio_sesion.html"
        }
        else {
            alert(responseText.messages);
        }
    }

    let json = {};
    let dataRegistro = new FormData(document.forms.formRegistro);
    dataRegistro.forEach(function(value, key){
        json[key] = value;
    });
    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}