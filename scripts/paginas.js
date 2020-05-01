var MAX = 15;
let imgpP = 4;
let pag = 1;
var nPags = 0;

function init() {
    pag = 1;
    getImgs();
}

function setPags(nPags) {
    var pags = document.getElementById("sigsPags");
    pags.innerHTML = "";

    let numPags = "";
    for(var i = 0; i < nPags; i++) {
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
    pages.children[pag-1].classList = "pag";

    if(e.innerText === "Sig") {
        if (pag < nPags) {
            pag++;
        }
    } else {
        pag = e.innerText;
    }

    pages.children[pag-1].classList = "pag ActPag";

    getImgs();
}

function getImgs() {
    var imagenes = document.getElementById("pubImgs");
    const url = `https://picsum.photos/v2/list?page=${pag}&limit=${imgpP}`;
    const xhr = new XMLHttpRequest();

    xhr.open('GET', "prueba.json", true);

    xhr.onload = function() {
        try {
            if(this.status === 200) {
                const memes = JSON.parse(this.responseText)["memesIndex"]["meme"];
                let lim = (pag-1)*imgpP, i = 0;
                if(pag === 1)
                {
                    nPags = Math.ceil(memes.length / imgpP);
                    setPags(nPags);
                }

                imagenes.innerHTML = " ";

                memes.forEach(function(meme, index){
                    if(index < lim || (index >= lim+imgpP))
                    {
                        return;
                    }
                    const addImg = `<div class="p-1 mb-2 col-s-12 col-m-8 offset-m-2 back-white rounded-border">
                                        <div class="row">
                                            <div class="col-s-12 offset-s-0 offset-m-2 col-m-8">
                                                <div class="pb-1">
                                                    <h3 class="pb-0p25">${meme.titulo}</h3>
                                                    <p>Creado por <i>${meme.usuario}</i></p>
                                                </div>
                                                <div class="pb-1"><img class="meme" src="${meme.rutaImagenMeme}" alt="meme" /></div>
                                                <div class="mb-1 tags">Tags: ${meme.tags}</div>
                                                <div class="mb-1">
                                                    <img class="icono" src="imagenes/pulgar_arriba.png">
                                                    <label class="pb-1">${meme.likes}</label>
                                                    <img class="icono" src="imagenes/pulgar_abajo.png">
                                                    <label>${meme.dislikes}</label>
                                                    <a href="publicacion.html" class="float-right">Comentarios</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`
                    imagenes.innerHTML += addImg;
                });
            }
        } catch (e) {}
    }

    try {
        xhr.send();
    } catch (e) {}
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}