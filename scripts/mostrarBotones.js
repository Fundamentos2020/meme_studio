window.addEventListener('load', mostrarBotones);

function mostrarBotones(){
    let sesion = getSesion();
    if(sesion != null){
        const botonPopulares = document.getElementById("btn-pop");
        var li = document.createElement("li");
        var a = document.createElement("a");
        a.href="moderar.html";
        a.innerText="Moderar";
        li.append(a);
        
        var li2 = document.createElement("li");
        var a2 = document.createElement("a");
        a2.href="generador.html";
        a2.innerText="Generador";
        li2.append(a2);
        
        botonPopulares.after(li);
        botonPopulares.after(li2);
    }
    
}