var moderacion_id;
const contenedorMemeInfo = document.getElementById('contenedor-info');
const imagenMeme = document.getElementById('imagen-meme');

function init(){
    obtenerModeraciones();
}

function obtenerModeraciones() {
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "moderaciones", true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                const pendientes = data.pendientes;
                if(data.total_registros > 0)
                {
                    console.log(pendientes);
                    moderacion_id = pendientes[0].moderacion_id;
                    obtenerMeme(pendientes[0].meme_id);
                }
                else
                    document.getElementById('contenedor-moderacion').innerHTML = 
                        `<h2 class=\"my-1 text-center\">
                            No hay memes pendientes por moderar
                        </h2>`;
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
                    
                    imagenMeme.src = '.' + meme.ruta_imagen_meme;
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}
