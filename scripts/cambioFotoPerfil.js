var canvas = document.getElementById("canvas");
var filename = null;

function cambiaFoto(){
    var ContextoCanvas = canvas.getContext("2d");
    var ObjetoImagen = new Image();

    ObjetoImagen.onload = function(){
        canvas.width = 200;
        canvas.height = canvas.width * (ObjetoImagen.height / ObjetoImagen.width);
        ContextoCanvas.drawImage(ObjetoImagen, 0, 0, canvas.width , canvas.height); 
    };
    ObjetoImagen.src = document.getElementById("image").src; 
};


var fileTag = document.getElementById("filePP"),
    preview = document.getElementById("image");
    
fileTag.addEventListener("DOMContentLoaded", function() {
  changeImage(this);
});

function changeImage(input) {
  var reader;
  if (input.files && input.files[0]) {
    reader = new FileReader();
    reader.onload = function(e) {
      preview.setAttribute('src', e.target.result);
      cambiaFoto();
    }
    reader.readAsDataURL(input.files[0]);
  }
}


function permitirCambios() {
    var x = document.getElementById("cambiar");
    var y = document.getElementById("cambiarD");
    var z = document.getElementById("guardar");
    
    if (x.style.display === "none" || y.style.display === "none" || z.style.display === "none") {
      x.style.display = "block";
      y.style.display = "block";
      z.style.display = "block";
    } else {
      x.style.display = "none";
      y.style.display = "none";
      z.style.display = "none";
    }
}

function addImagenPP(link, canvasId, filename) {
  link.href = document.getElementById(canvasId).toDataURL();
  link.download = filename;
}

  var p = document.getElementById('Descripcion');
  var btn = document.getElementById('guardarCambios');
  var txt = document.getElementById('txtDescripcion');
  /*btn.onclick = function(){
      p.textContent = txt.value;
  };*/

  const botonGuardarCambios = document.getElementById('guardarCambios');
  botonGuardarCambios.addEventListener('click', registrarCambios); 

  function registrarCambios(e) {
    e.preventDefault();

    let sesion = getSesion();
    if(sesion == null){
        alert("Inicia sesión para poder guardar los cambios");
        return;
    }

    const usuario_id = obtenerGetParam('usuario_id');
    var xhr = new XMLHttpRequest();

    xhr.open("PATCH", API + "usuarios/usuario_id="+usuario_id , true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        data = responseText.data;
        if(this.status === 201) {
            alert(responseText.messages);
            window.location.href = "./perfilUsuario.html?usuario_id=" + usuario_id;
        }
        else {
            alert(responseText.messages);
        }
    }

    let json = {};
    var fullPath = document.getElementById('filePP').value; 
    filename = fullPath.replace(/^.*\\/, "");

    json['ruta_imagen_perfil'] = addImagenPP(this, 'canvas', filename);
    if(txt.value != '')
      json['descripcion'] = txt.value;

    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}



var contenedorMemes = document.getElementById("contenedor-memes");
const botonVer = document.getElementById('boton-ver-memes');
botonVer.addEventListener('click', verMemes);

function verMemes(e) {
  var xhr =  new XMLHttpRequest();

    xhr.open("GET", API + "memes/usuario_id="+usuario_id, true);

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        if(this.status == 200){
            if(responseText.success === true){
                var data = responseText.data;
                
                const memes = data.memes;
                contenedorMemes.innerHTML = '';

                
                memes.forEach(function(meme, index){
                    if(index%3 == 0){
                        contenedorMemes.innerHTML += '<div class="row">';
                    }
                    let memeHTML = 
                        `<div class="col-m-3 col-s-11 m-1">
                            <div class="back-white rounded-border">
                                <div class="row">
                                    <div class="col-s-12 p-1">
                                        <div>
                                            <h4 class="pb-1">${meme.titulo}</h4>
                                        </div>
                                        <div>
                                            <div class="img-container">`;
                    if(meme.texto_superior != null)
                        memeHTML +=             `<div class="texto-superior">${meme.texto_superior}</div>`;
                    if(meme.texto_inferior != null)
                        memeHTML +=             `<div class="texto-inferior">${meme.texto_inferior}</div>`;
                
                    memeHTML+=`
                                                <img class="meme" src="${meme.ruta_imagen_meme}" alt="meme" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                        
                    contenedorMemes.innerHTML += memeHTML;
                    
                    if(index%3 == 0){
                        contenedorMemes.innerHTML += '</div>';
                    }
                });
            }
        }
        else {
            alert(responseText.messages);
        }
    };

    xhr.send();
}

