window.addEventListener('load', prevenirPagina);

function prevenirPagina(){
    let sesion = getSesion();
    if(sesion == null){
        alert("Inicia sesión para acceder a esta funcionalidad");
        window.location.href = 'inicio_sesion.html';
    }
}