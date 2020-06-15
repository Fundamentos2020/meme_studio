const usuario_id = obtenerGetParam('usuario_id');
var infoUsuario = document.getElementById("info-usuario");

function init(){
    obtenerUsuario();
    ocultarBotones();
}

function obtenerUsuario() {
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "usuarios/usuario_id="+usuario_id, true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                const usuario = data.usuarios[0];

                infoUsuario.innerHTML = 
                `    
                    <p class="full-name py-1">${usuario.nombre_completo}</p>
                    <p class="username">@${usuario.nombre_usuario}</p>
                `;
                if(usuario.descripcion == null)
                    infoUsuario.innerHTML += '<p class="desc mb-1" id="Descripcion">Sin descripción</p>';
                else
                    infoUsuario.innerHTML += `<p class="desc mb-1" id="Descripcion">${usuario.descripcion}</p>`;
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}

function ocultarBotones(){
    var sesion = getSesion();
    // Este usuario no debe ver los botones de editar y ver memes
    if(sesion == null || sesion.usuario_id != usuario_id){
        document.getElementById('boton-editar').style = "display: none;";
        document.getElementById('boton-ver-memes').style = "display: none;";
        document.getElementById('boton-cerrar-sesion').style = "display: none;";
    }
}

function cerrarSesion(){
    localStorage.removeItem('usuario_sesion');
    window.location.href = "index.html";
}