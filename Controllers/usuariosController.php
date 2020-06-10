<?php 

require_once('../Models/DB.php');
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

$row = $query->fetch(PDO::FETCH_ASSOC);

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

if($_SERVER['REQUEST_METHOD'] === 'PATCH'){
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

        if (isset($json_data->descripcion)) {
            $actualiza_descripcion = true;
            $campos_query .= "descripcion = :descripcion, ";
        }

        if (isset($json_data->ruta_imagen_perfil)) {
            $actualiza_ruta_imagen_perfil = true;
            $campos_query .= "ruta_imagen_perfil = :ruta_imagen_perfil, ";
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

        if($actualiza_descripcion === true) {
            $usuario->setDescripcion($json_data->descripcion);
            $up_descripcion = $usuario->getDescripcion();
            $query->bindParam(':descripcion', $up_descripcion, PDO::PARAM_STR);
        }

        if($actualiza_ruta_imagen_perfil === true) {
            $usuario->setRutaImagenPerfil($json_data->ruta_imagen_perfil);
            $up_ruta = $usuario->getRutaImagenPerfil();
            $query->bindParam(':ruta_imagen_perfil', $up_ruta, PDO::PARAM_STR);
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
            $response->addMessage("No se encontró al usuario después de actulizar");
            $response->send();
            exit();
        }

        $usuarioss = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $usuarioo = new Usuario($row['usuario_id'], $row['nombre_completo'], $row['nombre_usuario'], $row['ruta_imagen_perfil'], $row['descripcion']);

            $usuarioss[] = $usuarioo->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['usuarioss'] = $usuarioss;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Usuario actualizado");
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(TareaException $e) {
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
        $response->addMessage("Error en BD al actualizar la tarea");
        $response->send();
        exit();
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

//Validación de longitud...

$nombre_completo = trim($json_data->nombre_completo);
$nombre_usuario = trim($json_data->nombre_usuario);
$email = trim($json_data->email);
$contrasena = $json_data->contrasena;

try {
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

    $query = $connection->prepare('INSERT INTO usuarios (nombre_completo, nombre_usuario, email, contrasena) VALUES (:nombre_completo, :nombre_usuario, :email, :contrasena)');
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
catch(PDOException $e) {
    error_log('Error en BD - ' . $e);

    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error al crear usuario");
    $response->send();
    exit();
}

?>