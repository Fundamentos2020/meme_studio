let MAX = 100;
let imgpP = 5;
let pag = 1;
let nPags = 2;

function init() {
    pag = 1;
    nPags = Math.floor(MAX / imgpP);

    setPags();
    getImgs();
}

function setPags() {
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
    const xh = new XMLHttpRequest();

    xh.open('GET', url, true);

    xh.onload = function() {
        try {
            if(this.status === 200) {
                const imgs = JSON.parse(this.responseText) ;

                imagenes.innerHTML = " ";

                imgs.forEach(imagen => {
                    const addImg = `<div class="p-1 mb-2 col-s-12 col-m-8 offset-m-2 back-white rounded-border">
                                        <div class="row">
                                            <div class="col-s-12 offset-s-0 offset-m-2 col-m-8">
                                                <div class="pb-1">
                                                    <h3 class="pb-0p25">Titulo</h3>
                                                    <p><i>Usuario</i></p>
                                                </div>
                                                <div class="pb-1"><img class="meme" src="${imagen.download_url}" /></div>
                                                <div class="mb-1 tags">Tags: meme, fondo, divertido, slp, random</div>
                                                <div class="mb-1">
                                                    <img class="icono" src="imagenes/pulgar_arriba.png">
                                                    <label class="pb-1">${getRandomInt(30, 100)}</label>
                                                    <img class="icono" src="imagenes/pulgar_abajo.png">
                                                    <label>${getRandomInt(0, 50)}</label>
                                                    <a href="publicacion.html" class="float-right">Comentarios</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`
                    imagenes.innerHTML += addImg;
                });
            }
        } catch (e) {
        }
    }

    try {
        xh.send();
    } catch (e) {
    }
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}