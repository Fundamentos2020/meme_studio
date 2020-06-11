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