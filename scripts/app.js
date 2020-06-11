const API = "http://localhost:80/ProyectoFundamentosWeb/";
const client = "http://localhost:80/ProyectoFundamentosWeb/";

function getSesion() {
    var sesion = localStorage.getItem("usuario_sesion");
    
    if (sesion != null && sesion != "")
    {
        var sesion_json = JSON.parse(sesion);

        return sesion_json;
    }
    
    return null;
}

function refreshToken() {
    var sesion = getSesion();

    if (sesion == null) {
        window.location.href = client + 'inicio_sesion.html';
    }

    var xhttp = new XMLHttpRequest();

    xhttp.open("PATCH", api + "sesiones/" + sesion.id_sesion, false);
    xhttp.setRequestHeader("Authorization", sesion.token_acceso);
    xhttp.setRequestHeader("Content-Type", "application/json");

    var json = { "token_actualizacion": sesion.token_actualizacion };
    var json_string = JSON.stringify(json);

    xhttp.send(json_string);

    var data = JSON.parse(xhttp.responseText);

    if (data.success === true){
        localStorage.setItem('usuario_sesion', JSON.stringify(data.data));
        window.location.href = client;
    }
    else{
        alert(data.messages);
        window.location.href = client;
    }
}

function obtenerGetParam(nombre_param){
    var url_string = window.location.href;
    var url = new URL(url_string);
    var value = url.searchParams.get(nombre_param);
    return value;
}