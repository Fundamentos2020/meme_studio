var canvas = document.getElementById("canvas");
var textoArriba;
var textoAbajo;

function GenerarMeme(){
        textoArriba=document.getElementById("textoArriba").value;
        textoAbajo=document.getElementById("textoAbajo").value;
        var ContextoCanvas = canvas.getContext("2d");
        var ObjetoImagen = new Image();

        ObjetoImagen.onload = function(){
            canvas.width = 605;
            canvas.height = canvas.width * (ObjetoImagen.height / ObjetoImagen.width);
            ContextoCanvas.drawImage(ObjetoImagen, 0, 0, canvas.width , canvas.height);
            
            
            // Datos texto Meme (font)
            ContextoCanvas.lineWidth  = 5;
            ContextoCanvas.font = '15pt sans-serif';
            ContextoCanvas.strokeStyle = 'black';
            ContextoCanvas.fillStyle = 'white';
            ContextoCanvas.textAlign = 'center';
            ContextoCanvas.lineJoin = 'round';
        
            textoAbajo = textoAbajo.toUpperCase(); 
            x = canvas.width/2;
            y = canvas.height - canvas.height/7.4;
            
            ContextoCanvas.strokeText(textoAbajo, x, y);
            ContextoCanvas.fillText(textoAbajo, x, y);
        
            textoArriba = textoArriba.toUpperCase();
            ContextoCanvas.strokeText(textoArriba, x, 30);
            ContextoCanvas.fillText(textoArriba, x, 30);
    };
	 ObjetoImagen.src = document.getElementById("image").src; 
};


var fileTag = document.getElementById("fileMeme"),
    preview = document.getElementById("image");
    
fileTag.addEventListener("change", function() {
  changeImage(this);
});

function changeImage(input) {
  var reader;
  if (input.files && input.files[0]) {
    reader = new FileReader();
    reader.onload = function(e) {
      preview.setAttribute('src', e.target.result);
      GenerarMeme();
    }
    reader.readAsDataURL(input.files[0]);
  }
}

var imagen=document.getElementsByClassName("imgthumbnail");
for(i=0;i<imagen.length;i++){
    imagen[i].onclick = function() { 
        document.getElementById("image").src=this.src;
        GenerarMeme(); 
    };
}

function mostrarPredeterminados() {
    var m = document.getElementById("mostrarPlantillas");
    if (m.style.display === "none") {
      m.style.display = "block";
    } else {
      m.style.display = "none";
    }
  }


  function getNombre() {
    var fullPath = document.getElementById('fileMeme').files[0].name; 
    var filename = fullPath.replace(/^.*\\/, "");
    // or, try this, 
    // var filename = fullPath.split("/").pop();
}


  const botonGenerarMeme = document.getElementById('botonGenerar');
  botonGenerarMeme.addEventListener('click', registrarMeme); 

  function registrarMeme(e) {
    e.preventDefault();

    let sesion = getSesion();
    if(sesion == null){
        alert("Inicia sesión para poder publicar");
        return;
    }

    var reader = new FileReader();

    reader.onload = function (event) {
    document.getElementById("fileMeme").src = event.target.result;
    };

    var xhr = new XMLHttpRequest();

    xhr.open("POST", API + "memes" , true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        data = responseText.data;
        if(this.status === 201) {
            alert(responseText.messages);
            window.location.href = "./index.html";
        }
        else {
            alert(responseText.messages);
        }
    }

    let json = {};
    json['usuario_id'] = sesion.usuario_id;
    json['likes'] = 0;
    json['dislikes'] = 0;
    json['estado_meme'] = 'ACEPTADO';
    json['ruta_imagen_meme'] = filename;
    json['titulo'] = document.getElementById('TituloMeme').value;
    json['texto_superior'] = document.getElementById('textoArriba').value;
    json['texto_inferior'] = document.getElementById('textoAbajo').value;
    json['fecha_creacion'] = obtenerFechaActual();
    json['fecha_publicacion'] = obtenerFechaActual();

    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}