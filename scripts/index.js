let memesPorPagina = 5;
let pagActual = 1;
var numeroPaginas = 0;

var contenedorMemes = document.getElementById("contenedor-memes");

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

    xhr.open("GET", API + "memes", true);

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
                    
                    contenedorMemes.innerHTML += crearHTMLMeme(meme);
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}