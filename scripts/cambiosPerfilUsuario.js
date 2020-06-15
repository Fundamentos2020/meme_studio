function initUsuario(){
  permitirCambios();
  mostrarPredeterminados(); 
  permitirVer(); 
  verFotoPerfil();
}

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
    ObjetoImagen.src = document.getElementById("imagePP").src; 
};


var imagen = document.getElementsByClassName("imgthumbnail");
for(i=0;i<imagen.length;i++){
    imagen[i].onclick = function() { 
        document.getElementById("imagePP").src = this.src;
        //cambiaFoto(); 
    };
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

function permitirVer() {
  var x = document.getElementById("verMemesGuardados");
  
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

function mostrarPredeterminados() {
  var m = document.getElementById("mostrarPlantillas");
  if (m.style.display === "none") {
    m.style.display = "block";
  } else {
    m.style.display = "none";
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
    if(filename != null)
      json['ruta_imagen_perfil'] = document.getElementById('imagePP').src;
    else if(document.getElementById('imagePP').src != null)
      json['ruta_imagen_perfil'] = document.getElementById('imagePP').src;
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
                        contenedorMemes.innerHTML += '<div class="row"><div class="offset-m-1">';
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
                                                <a href="publicacion.html?meme_id=${meme.meme_id}"><img class="meme" src="${meme.ruta_imagen_meme}" alt="meme" /></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                        
                    contenedorMemes.innerHTML += memeHTML;
                    
                    if(index%3 == 2){
                        contenedorMemes.innerHTML += '</div></div>';
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


var imagenPerfil = document.getElementById("mostrar");
function verFotoPerfil() {
  var xhr =  new XMLHttpRequest();

  xhr.open("GET", API + "usuarios/usuario_id="+usuario_id, true);

  xhr.onload = function() {
      var responseText = JSON.parse(this.responseText);
      if(this.status == 200){
          if(responseText.success === true){
              var data = responseText.data;
              
              const datos = data.usuarios[0];
              imagenPerfil.innerHTML = 
                `
                  <img src="${datos.ruta_imagen_perfil}" alt="Profile Image" class="profile-img" id="imagePP"" />
                `;
          }
      }
      else {
          alert(responseText.messages);
      }
  };

  xhr.send();
}