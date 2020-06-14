function incrementarLikes(meme_id){
    let sesion = getSesion();
    if(sesion == null){
        alert("Inicia sesión para poder darle like a un meme");
        return;
    }
    const contadorLikes = document.getElementById('countLikes' +meme_id);
    
    xhr = new XMLHttpRequest();

    xhr.open("PATCH", API + "memes/meme_id=" + meme_id , true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        data = responseText.data;
        if(this.status === 200) {
            contadorLikes.innerText = parseInt(contadorLikes.innerText) + 1;
        }
    }

    let json = {};
    json['likes'] = parseInt(contadorLikes.innerText) + 1;
    var json_string = JSON.stringify(json);

    xhr.send(json_string);
}


function incrementarDislikes(meme_id){
    let sesion = getSesion();
    if(sesion == null){
        alert("Inicia sesión para poder darle dislike a un meme");
        return;
    }
    const contadorDislikes = document.getElementById('countDislikes' +meme_id);
    
    xhr = new XMLHttpRequest();

    xhr.open("PATCH", API + "memes/meme_id=" + meme_id , true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        var responseText = JSON.parse(this.responseText);
        data = responseText.data;
        if(this.status === 200) {
            contadorDislikes.innerText = parseInt(contadorDislikes.innerText) +1;
        }
    }

    let json = {};
    json['dislikes'] = parseInt(contadorDislikes.innerText) + 1;
    var json_string = JSON.stringify(json);

    xhr.send(json_string);    
}