const meme_id = obtenerGetParam('meme_id');
var contenedorMeme = document.getElementById("contenedor-meme");
var listaComentarios = document.getElementById("comments-list");

function init(){
    obtenerMeme();
    obtenerComentarios();
}

function obtenerMeme() {
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "memes/meme_id="+meme_id, true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                const memes = data.memes;
                contenedorMeme.innerHTML = "";

                memes.forEach(function(meme){
                    let addImg = 
                        `
                        <div class="pb-1">
                            <h3 class="pb-0p25">${meme.titulo}</h3>
                            <p><i>${meme.nombre_usuario}</i></p>
                        </div>
                        <div class="pb-1">
                            <div class="img-container">`;
                        
                    if(meme.texto_superior != null)
                        addImg += `<div class="texto-superior">${meme.texto_superior}</div>`;
                    if(meme.texto_inferior != null)
                        addImg += `<div class="texto-inferior">${meme.texto_inferior}</div>`;
                
                    addImg+=`<img class="meme" src="${meme.ruta_imagen_meme}" alt="meme" />
                        </div>
                    </div>
                    <div class="mb-1 tags">Tags: `;

                            if(meme.tags.length != 0)
                                meme.tags.forEach(function(tag, index){ 
                                    addImg += tag; 
                                    if(index != meme.tags.length-1)
                                        addImg += ", ";
                                });
                            else
                                addImg += "No hay tags asignados";
                    addImg +=
                      `</div>
                       <div class="mb-1">
                            <img class="icono" src="../imagenes/pulgar_arriba.png" onclick="incrementarLikes(${meme.meme_id});">
                            <label id="countLikes${meme.meme_id}" class="pb-1">${meme.likes}</label>
                            <img class="icono" src="../imagenes/pulgar_abajo.png" onclick="incrementarDislikes(${meme.meme_id});">
                            <label id="countDislikes${meme.meme_id}">${meme.dislikes}</label>
                        </div>
                        `
                    contenedorMeme.innerHTML += addImg;
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}

function obtenerComentarios() {
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "comentarios/meme_id=" +meme_id, true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;

                const comentarios = data.comentarios;
                
                const tiempoActual = Date.parse(obtenerFechaActual());

                listaComentarios.innerHTML = "";
                comentarios.forEach(function(comentario){
                    var fecha_comentario = Date.parse(comentario.fecha_comentario);
                    var horasDiferencia = Math.abs(tiempoActual - fecha_comentario) / 36e5;
                    let nuevoComentarioHtml = 
                    `
                    <li>
                        <div>
                            <div class="comment-avatar"><img src="${comentario.ruta_imagen_perfil}" alt="imagen_perfil"></div>
                            
                            <div class="comment-box">
                                <div class="comment-head">
                                    <h6 class="comment-name">
                                        <a href="perfilUsuario.html?usuario_id=${comentario.usuario_id}">
                                            ${comentario.nombre_usuario}
                                        </a>
                                    </h6>
                                    <span>hace `;
                    if(horasDiferencia < 1){
                        nuevoComentarioHtml += horasDiferencia*60 + " minuto(s)";
                    }
                    else {
                        horasDiferencia = Math.floor(horasDiferencia);
                        nuevoComentarioHtml += horasDiferencia + " hora(s)";
                    }
                    

                    nuevoComentarioHtml += `</div>
                                <div class="comment-content">
                                    ${comentario.contenido}
                                </div>
                            </div>
                        </div>
                    </li>
                    `;
                    listaComentarios.innerHTML += nuevoComentarioHtml;
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}