function redirigirUsuario(botonUsuario)
{
    let sesion = getSesion();

    if(sesion == null){
        botonUsuario.href = 'inicio_sesion.html';
        return false;
    }
    else{
        botonUsuario.href = 'perfilUsuario.html?usuario_id=' + sesion.usuario_id;
        return false;
    }
}