const botonLogin = document.getElementById('boton-login');

botonLogin.addEventListener('click', login);

function login(e) {
    e.preventDefault();

    var xhr = new XMLHttpRequest();

    xhr.open("POST", API + "sesiones", true);
    xhr.withCredentials = true;
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        var data = responseText.data;
        if(this.status === 201) {
            localStorage.setItem('usuario_sesion', JSON.stringify(data));
            window.location.href = "./index.html"
        }
        else {
            alert(responseText.messages);
        }
    }

    let json = {};
    let dataRegistro = new FormData(document.forms.formLogin);
    dataRegistro.forEach(function(value, key){
        json[key] = value;
    });
    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}