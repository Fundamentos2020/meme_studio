let memesPorPagina = 5;
let pagActual = 1;
var numeroPaginas = 0;

var contenedorMemes = document.getElementById("contenedor-memes");
const API = 'https://memestudiogs.000webhostapp.com/';


function init() {
    pagActual = 1;
    obtenerMemes();
}

function setPags(numeroPaginas) {
    var pags = document.getElementById("sigsPags");
    pags.innerHTML = "";

    let numPags = "";
    for(var i = 0; i < numeroPaginas; i++) {
        if(i === 0) {
            numPags += `<div class="pag ActPag" onclick="clickSigPag(this);">${i+1}</div>`;
        } else {
            numPags += `<div class="pag" onclick="clickSigPag(this);">${i+1}</div>`;
        }
    }
    numPags += `<div class="pag" onclick="clickSigPag(this);">Sig</div>`;

    pags.innerHTML = numPags;
}

function clickSigPag(e) {
    window.scrollTo(0, 0); 
    var pages = document.getElementById("sigsPags");
    pages.children[pagActual-1].classList = "pag";

    if(e.innerText === "Sig") {
        if (pagActual < numeroPaginas) {
            pagActual++;
        }
    } else {
        pagActual = e.innerText;
    }

    pages.children[pagActual-1].classList = "pag ActPag";

    obtenerMemes();
}

function obtenerMemes() {
    var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "memes/populares=semanal", true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;


                const memes = data.memes;
                let lim = (pagActual-1)*memesPorPagina, i = 0;
                if(pagActual === 1)
                {
                    numeroPaginas = Math.ceil(memes.length / memesPorPagina);
                    setPags(numeroPaginas);
                }

                contenedorMemes.innerHTML = " ";

                memes.forEach(function(meme, index){
                    if(index < lim || (index >= lim+memesPorPagina))
                    {
                        return;
                    }
                    let addImg = `<div class="p-1 mb-2 col-s-12 col-m-8 offset-m-2 back-white rounded-border">
                                        <div class="row">
                                            <div class="col-s-12 offset-s-0 offset-m-2 col-m-8">
                                                <div class="pb-1">
                                                    <h3 class="pb-0p25">${meme.titulo}</h3>
                                                    <p>Creado por <i>${meme.nombre_usuario}</i></p>
                                                </div>
                                                <div class="pb-1"><img class="meme" src="${meme.ruta_imagen_meme}" alt="meme" /></div>
                                                <div class="mb-1 tags">Tags: `;

                                                    if(meme.tags.length != 0)
                                                        meme.tags.forEach(function(tag, index){ 
                                                            addImg += tag; 
                                                            if(index != meme.tags.length-1)
                                                                addImg += ", ";
                                                        });
                                                    else
                                                        addImg += "No hay tags asignados";

                                     
                                      addImg +=`<div class="mb-1">
                                                    <img class="icono" src="imagenes/pulgar_arriba.png">
                                                    <label class="pb-1">${meme.likes}</label>
                                                    <img class="icono" src="imagenes/pulgar_abajo.png">
                                                    <label>${meme.dislikes}</label>
                                                    <a href="publicacion.html?meme_id=${meme.meme_id}" class="float-right">Comentarios</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`
                    contenedorMemes.innerHTML += addImg;
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}