const botonComentario = document.getElementById('botonComentario');
botonComentario.addEventListener('click', registrarComentario);

function registrarComentario(e) {
    e.preventDefault();

    let sesion = getSesion();
    if(sesion == null){
        alert("Inicia sesión para poder comentar");
        return;
    }

    const meme_id = obtenerGetParam('meme_id');
    var xhr = new XMLHttpRequest();

    xhr.open("POST", API + "comentarios" , true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        data = responseText.data;
        if(this.status === 201) {
            alert(responseText.messages);
            window.location.href = "./publicacion.html?meme_id=" + meme_id;
        }
        else {
            alert(responseText.messages);
        }
    }

    let json = {};
    json['usuario_id'] = sesion.usuario_id;
    json['meme_id'] = meme_id;
    json['contenido'] = document.getElementById('contenidoComentario').value;
    json['fecha_comentario'] = obtenerFechaActual();

    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}