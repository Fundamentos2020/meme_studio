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
    var fullPath = document.getElementById('filePP').value; 
    //filename = fullPath.replace(/^.*\\/, "");

    json['ruta_imagen_perfil'] = addImagenPP(this, 'canvas', document.getElementById('filePP').value);
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
                contenedorMemes.innerHTML = "";

                
                  memes.forEach(function(meme){
                      let addImg = 
                          `
                          <div class="pb-1"><img class="meme" src="https://picsum.photos/800/600" /></div>
                          `
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

