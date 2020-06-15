function crearHTMLMeme(meme){
    console.log(meme);
    let memeHTML = 
    `<div class="p-1 mb-2 col-s-12 col-m-8 offset-m-2 back-white rounded-border">
        <div class="row">
            <div class="col-s-12 offset-s-0 offset-m-2 col-m-8">
                <div class="pb-1">
                    <h3 class="pb-0p25">${meme.titulo}</h3>
                    <p>Creado por <i>${meme.nombre_usuario}</i></p>
                </div>
                <div class="pb-1">
                    <div class="img-container">`;
    if(meme.texto_superior != null)
        memeHTML += `<div class="texto-superior">${meme.texto_superior}</div>`;
    if(meme.texto_inferior != null)
        memeHTML += `<div class="texto-inferior">${meme.texto_inferior}</div>`;

             memeHTML+=`<img class="meme" src="${meme.ruta_imagen_meme}" alt="meme" />
                    </div>
                </div>
                <div class="mb-1 tags">Tags: `;

    if(meme.tags.length != 0)
        meme.tags.forEach(function(tag, index){ 
            memeHTML += tag; 
            if(index != meme.tags.length-1)
            memeHTML += ", ";
        });
    else
        memeHTML += "No hay tags asignados";

                                     
        memeHTML +=
               `</div>
                <div class="mb-1">
                    <img class="icono" src="../imagenes/pulgar_arriba.png" onclick="incrementarLikes(${meme.meme_id});">
                    <label id="countLikes${meme.meme_id}" class="pb-1">${meme.likes}</label>
                    <img class="icono" src="../imagenes/pulgar_abajo.png" onclick="incrementarDislikes(${meme.meme_id});">
                    <label id="countDislikes${meme.meme_id}">${meme.dislikes}</label>
                    <a href="publicacion.html?meme_id=${meme.meme_id}" class="float-right">Comentarios</a>
                </div>
            </div>
        </div>
    </div>`
    return memeHTML;
}