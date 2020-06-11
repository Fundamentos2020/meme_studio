var canvas = document.getElementById("canvas");


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
    
fileTag.addEventListener("change", function() {
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

var imagen=document.getElementsByClassName("imgthumbnail");
for(i=0;i<imagen.length;i++){
    imagen[i].onclick = function() { 
        document.getElementById("image").src=this.src;
        cambiaFoto(); 
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

function guardarCambios() {
    var p = document.getElementById('Descripcion');
    var btn = document.getElementById('guardarCambios');
    var txt = document.getElementById('txtDescripcion');
    btn.onclick = function(){
        p.textContent = txt.value;
    };
}

function mostrarMemes() {
    var m = document.getElementById("muestraMemes");
    
    if (m.style.display === "none") {
      m.style.display = "block";
    } else {
      m.style.display = "none";
    }
  }
