<?php 

require_once('../Models/BD.php');
require_once('../Models/Usuario.php');
require_once('../Models/Response.php');

try {
    $connection = DB::getConnection();
}
catch (PDOException $e){
    error_log("Error de conexión - " . $e);

    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error en conexión a Base de datos");
    $response->send();
    exit();
}

if(array_key_exists("usuario_id", $_GET)){
    $usuario_id = $_GET['usuario_id'];

    if ($usuario_id == '' || !is_numeric($usuario_id)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El id de usuario no puede estar vacío y debe ser numérico");
        $response->send();
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        try{
            $query = $connection->prepare('SELECT usuario_id, nombre_completo, nombre_usuario, ruta_imagen_perfil, descripcion FROM usuarios WHERE usuario_id = :usuario_id');
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $query->execute();
    
            $rowCount = $query->rowCount();
    
            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No se encontró al usuario");
                $response->send();
                exit();
            }
    
            $usuarios = array();
    
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $usuario = array();
                $usuario['usuario_id'] = $row['usuario_id'];
                $usuario['nombre_completo'] = $row['nombre_completo'];
                $usuario['nombre_usuario'] = $row['nombre_usuario'];
                $usuario['ruta_imagen_perfil'] = $row['ruta_imagen_perfil'];
                $usuario['descripcion'] = $row['descripcion'];

                $usuarios[] = $usuario;
            }
    
            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['usuarios'] = $usuarios;
    
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch(UsuarioException $e) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        }
        catch(PDOException $e) {
            error_log("Error en BD - " . $e);
    
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error en BD al recuperar el usuario");
            $response->send();
            exit();
        }
    }
    else if($_SERVER['REQUEST_METHOD'] === 'PATCH'){
        try {
            if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Encabezado "Content type" no es JSON');
                $response->send();
                exit();
            }
    
            $patchData = file_get_contents('php://input');
    
            if (!$json_data = json_decode($patchData)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
                $response->send();
                exit();
            }
    
            $actualiza_descripcion = false;
            $actualiza_ruta_imagen_perfil = false;
    
            $campos_query = "";
    
            if (isset($json_data->ruta_imagen_perfil)) {
                $actualiza_ruta_imagen_perfil = true;
                $campos_query .= "ruta_imagen_perfil = :ruta_imagen_perfil, ";
            }

            if (isset($json_data->descripcion)) {
                $actualiza_descripcion = true;
                $campos_query .= "descripcion = :descripcion, ";
            }
    
            $campos_query = rtrim($campos_query, ", ");
    
            if ($actualiza_descripcion === false && $actualiza_ruta_imagen_perfil === false) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("No hay campos para actualizar");
                $response->send();
                exit();
            }
    
            $query = $connection->prepare('SELECT usuario_id, nombre_completo, nombre_usuario, ruta_imagen_perfil, descripcion FROM usuarios WHERE usuario_id = :usuario_id');
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $query->execute();
    
            $rowCount = $query->rowCount();
    
            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No se encontró el usuario");
                $response->send();
                exit();
            }
    
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $usuario = new Usuario($row['usuario_id'], $row['nombre_completo'], $row['nombre_usuario'], $row['ruta_imagen_perfil'], $row['descripcion']);
            }
    
            $cadena_query = 'UPDATE usuarios SET ' . $campos_query . ' WHERE usuario_id = :usuario_id';
            $query = $connection->prepare($cadena_query);
    
            
    
            if($actualiza_ruta_imagen_perfil === true) {
                $usuario->setRutaImagenPerfil($json_data->ruta_imagen_perfil);
                $up_ruta = $usuario->getRutaImagenPerfil();
                $query->bindParam(':ruta_imagen_perfil', $up_ruta, PDO::PARAM_STR);
            }

            if($actualiza_descripcion === true) {
                $usuario->setDescripcion($json_data->descripcion);
                $up_descripcion = $usuario->getDescripcion();
                $query->bindParam(':descripcion', $up_descripcion, PDO::PARAM_STR);
            }
    
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $query->execute();
    
            $rowCount = $query->rowCount();
    
            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al actualizar al usuario");
                $response->send();
                exit();
            }
    
            $query = $connection->prepare('SELECT usuario_id, nombre_completo, nombre_usuario, ruta_imagen_perfil, descripcion FROM usuarios WHERE usuario_id = :usuario_id');
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $query->execute();
    
            $rowCount = $query->rowCount();
    
            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No se encontró al usuario después de actualizar");
                $response->send();
                exit();
            }
    
            $usuarios = array();
    
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $usuario = new Usuario($row['usuario_id'], $row['nombre_completo'], $row['nombre_usuario'], $row['ruta_imagen_perfil'], $row['descripcion']);
    
                $usuarios[] = $usuario->getArray();
            }
    
            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['usuarios'] = $usuarios;
    
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Usuario actualizado");
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch(UsuarioException $e) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        }
        catch(PDOException $e) {
            error_log("Error en BD - " . $e);
    
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error en BD al actualizar el usuario");
            $response->send();
            exit();
        }
    }
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
}
else if(empty($_GET)){
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
    
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Encabezado Content Type no es JSON");
        $response->send();
        exit();
    }
    
    $postData = file_get_contents('php://input');
    
    if (!$json_data = json_decode($postData)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El cuerpo de la solicitud no es un JSON válido");
        $response->send();
        exit();
    }
    
    if (!isset($json_data->nombre_completo) || !isset($json_data->nombre_usuario)  || !isset($json_data->email) || !isset($json_data->contrasena)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        (!isset($json_data->nombre_completo) ? $response->addMessage("El nombre completo es obligatorio") : false);
        (!isset($json_data->nombre_usuario) ? $response->addMessage("El nombre de usuario es obligatorio") : false);
        (!isset($json_data->email) ? $response->addMessage("El email es obligatorio") : false);
        (!isset($json_data->contrasena) ? $response->addMessage("La contraseña es obligatoria") : false);
        $response->send();
        exit();
    }
    
    //Validación de longitud...
    
    $nombre_completo = trim($json_data->nombre_completo);
    $nombre_usuario = trim($json_data->nombre_usuario);
    $email = trim($json_data->email);
    $contrasena = trim($json_data->contrasena);

    try {
        $usuario = new Usuario(
            null,
            'USUARIO',
            $nombre_completo,
            $nombre_usuario,
            $email,
            $contrasena,
            null,
            null
        );

        $rol = $usuario->getRol();
        $nombre_completo = $usuario->getNombreCompleto();
        $nombre_usuario = $usuario->getNombreUsuario();
        $email = $usuario->getEmail();
        $contrasena = $usuario->getContrasena();

    
        $query = $connection->prepare('SELECT usuario_id FROM usuarios WHERE nombre_usuario = :nombre_usuario');
        $query->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
        $query->execute();
    
        $rowCount = $query->rowCount();
    
        if($rowCount !== 0) {
            $response = new Response();
            $response->setHttpStatusCode(409);
            $response->setSuccess(false);
            $response->addMessage("El nombre de usuario ya existe");
            $response->send();
            exit();
        }
    
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    
        $query = $connection->prepare('INSERT INTO usuarios (rol, nombre_completo, nombre_usuario, email, contrasena) 
                VALUES (:rol, :nombre_completo, :nombre_usuario, :email, :contrasena)');
        $query->bindParam(':rol', $rol, PDO::PARAM_STR);
        $query->bindParam(':nombre_completo', $nombre_completo, PDO::PARAM_STR);
        $query->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
        $query->execute();
    
        $rowCount = $query->rowCount();
    
        if($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al crear usuario - inténtelo de nuevo");
            $response->send();
            exit();
        }
    
        $ultimoID = $connection->lastInsertId();
    
        $returnData = array();
        $returnData['usuario_id'] = $ultimoID;
        $returnData['nombre_completo'] = $nombre_completo;
        $returnData['nombre_usuario'] = $nombre_usuario;
        
    
        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Usuario creado");
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch (UsuarioException $e) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log('Error en BD - ' . $e);
    
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al crear usuario");
        $response->send();
        exit();
    }
}
else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Ruta no encontrada");
    $response->send();
    exit();
}

?>