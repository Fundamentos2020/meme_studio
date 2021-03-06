var moderacion_id;
var meme_id;
const contenedorMemeInfo = document.getElementById('contenedor-info');
const imagenMeme = document.getElementById('imagen-meme');
const contenedorImg = document.getElementById('contenedor-img');
const botonAceptar = document.getElementById('botonAceptar');
const botonRechazar = document.getElementById('botonRechazar');

botonAceptar.addEventListener('click', generarModeracion);
botonRechazar.addEventListener('click', generarModeracion);

function init(){
    obtenerModeraciones();
}

function obtenerModeraciones() {
    let sesion = getSesion();
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "moderaciones", true);
    xhr.setRequestHeader("Authorization", sesion.token_acceso);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                const pendientes = data.pendientes;
                if(data.total_registros > 0)
                {
                    moderacion_id = pendientes[0].moderacion_id;
                    meme_id = pendientes[0].meme_id;
                    obtenerMeme(pendientes[0].meme_id);
                }
                else
                    document.getElementById('contenedor-moderacion').innerHTML = 
                        `<h2 class=\"my-1 text-center\">
                            No hay memes pendientes por moderar
                        </h2>`;
            }
        }
        else if(this.status == 401) {
            if (responseText.messages.indexOf("Token de acceso ha caducado") >= 0) {
                refreshToken();
                window.location.reload();
            }
            else {
                alert(responseText.messages);
                window.location.href = "index.html";
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}



function obtenerMeme(meme_id) {
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "memes/meme_id="+meme_id, true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                const memes = data.memes;
                contenedorMemeInfo.innerHTML = "";

                memes.forEach(function(meme){
                    let addImg = 
                        `
                        <h4 class="mb-1">${meme.titulo}</h4>
                        <p class="">Usuario: <i>${meme.nombre_usuario}</i></p>
                        <div class="mb-1 tags">Tags: ` 
                            if(meme.tags.length != 0)
                                meme.tags.forEach(function(tag, index){ 
                                    addImg += tag; 
                                    if(index != meme.tags.length-1)
                                        addImg += ", ";
                                });
                            else
                                addImg += "No hay tags asignados";
                    addImg += `</div>`
                    contenedorMemeInfo.innerHTML += addImg;

                    
                    contenedorImg.innerHTML=`<div class="img-container">`;
                    if(meme.texto_superior != null)
                        contenedorImg.innerHTML += `<div class="texto-superior">${meme.texto_superior}</div>`;
                    if(meme.texto_inferior != null)
                        contenedorImg.innerHTML += `<div class="texto-inferior">${meme.texto_inferior}</div>`;

                    contenedorImg.innerHTML+=`<img class="meme" src="${meme.ruta_imagen_meme}" alt="meme" /></div>`;

                    
                    imagenMeme.src = meme.ruta_imagen_meme;
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}


function generarModeracion(e){
    e.preventDefault();
    let sesion = getSesion();
    
    var xhr =  new XMLHttpRequest();

    xhr.open("PATCH", API + "moderaciones/moderacion_id="+moderacion_id, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Authorization", sesion.token_acceso);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                modificarMeme(data.moderaciones[0].estatus_moderacion);
                alert(responseText.messages);
                window.location.reload();
            }
        }
        else if(this.status == 401) {
            if (responseText.messages.indexOf("Token de acceso ha caducado") >= 0) {
                refreshToken();
                window.location.reload();
            }
            else {
                alert(responseText.messages);
                window.location.href = "index.html";
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    let json = {};
    // Se aceptó el meme
    if(e.currentTarget.value == "Aceptar ✓")
        json['estatus_moderacion'] = 'ACEPTADO';
    else
        json['estatus_moderacion'] = 'RECHAZADO';
    json['retroalimentacion'] = document.getElementById('retroalimentacion').value;

    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}

function modificarMeme(estado_meme){
    var xhr =  new XMLHttpRequest();

    xhr.open("PATCH", API + "memes/meme_id="+meme_id, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                alert(responseText.messages);
                window.location.href = './moderar.html'; 
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    let json = {};
    json['estado_meme'] = estado_meme;
    if(estado_meme == 'ACEPTADO')
        json['fecha_publicacion'] = obtenerFechaActual();

    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}
