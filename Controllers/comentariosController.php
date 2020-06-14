<?php

require_once('../Models/BD.php');
require_once('../Models/Comentario.php');
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


if(array_key_exists("meme_id", $_GET)){
    // host/comentarios/meme_id={id}
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $meme_id = $_GET['meme_id'];

        if ($meme_id == '' || !is_numeric($meme_id)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El id del meme no puede estar vacío y debe ser numérico");
            $response->send();
            exit();
        }


        try {
            $sql = 'SELECT comentario_id, comentarios.usuario_id, meme_id, contenido,
                        DATE_FORMAT(fecha_comentario, "%Y-%m-%d %H:%i") fecha_comentario, usuarios.nombre_usuario, usuarios.ruta_imagen_perfil
                    FROM comentarios INNER JOIN usuarios WHERE comentarios.usuario_id = usuarios.usuario_id 
                    AND meme_id = :meme_id ORDER BY fecha_comentario DESC';

            $query = $connection->prepare($sql);
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);

            $query->execute();

            $rowCount = $query->rowCount();
            $comentarios = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $comentario = new Comentario($row['comentario_id'], $row['usuario_id'], $row['meme_id'], $row['contenido'], $row['fecha_comentario']);
                $comentario_completo = $comentario->getArray();

                $comentario_completo['nombre_usuario'] = $row['nombre_usuario'];
                $comentario_completo['ruta_imagen_perfil'] = $row['ruta_imagen_perfil'];

                $comentarios[] = $comentario_completo;
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['comentarios'] = $comentarios;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (ComentarioException $e){
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        }
        catch (PDOException $e){
            error_log("Error de consulta - " . $e);
        
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error en conexión a Base de datos");
            $response->send();
            exit();
        }
    }
    else{
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
}
else if (empty($_GET)) {
    // POST host/comentarios
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        try {
            if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Encabezado "Content type" no es JSON');
                $response->send();
                exit();
            }

            $postData = file_get_contents('php://input');

            if (!$json_data = json_decode($postData)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
                $response->send();
                exit();
            }

            if (!isset($json_data->usuario_id) || !isset($json_data->meme_id) || !isset($json_data->contenido) || !isset($json_data->fecha_comentario)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->usuario_id) ? $response->addMessage('El id de usuario es obligatorio') : false);
                (!isset($json_data->meme_id) ? $response->addMessage('El id de meme es obligatorio') : false);
                (!isset($json_data->contenido) ? $response->addMessage('El contenido es obligatorio') : false);
                (!isset($json_data->fecha_comentario) ? $response->addMessage('La fecha del comentario es obligatoria') : false);
                $response->send();
                exit();
            }

            $comentario = new Comentario(
                null,
                $json_data->usuario_id,
                $json_data->meme_id,
                $json_data->contenido,
                $json_data->fecha_comentario
            );

            $usuario_id = $comentario->getUsuarioID();
            $meme_id = $comentario->getMemeID();
            $contenido = $comentario->getContenido();
            $fecha_comentario =$comentario->getFechaComentario();

            $sql = 'INSERT INTO comentarios (usuario_id, meme_id, contenido, fecha_comentario)
                    VALUES (:usuario_id, :meme_id, :contenido, STR_TO_DATE(:fecha_comentario, \'%Y-%m-%d %H:%i\'))';

            $query = $connection->prepare($sql);
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->bindParam(':contenido', $contenido, PDO::PARAM_STR);
            $query->bindParam(':fecha_comentario', $fecha_comentario, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al crear el comentario");
                $response->send();
                exit();
            }

            $ultimo_ID = $connection->lastInsertId();

            $sql = 'SELECT comentario_id, usuario_id, meme_id, contenido,
                        DATE_FORMAT(fecha_comentario, "%Y-%m-%d %H:%i") fecha_comentario
                    FROM comentarios WHERE comentario_id = :comentario_id';

            $query = $connection->prepare($sql);
            $query->bindParam(':comentario_id', $ultimo_ID, PDO::PARAM_INT);
            $query->execute();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al obtener comentario después de crearlo");
                $response->send();
                exit();
            }

            $comentarios = array();
                
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $comentario = new Comentario($row['comentario_id'], $row['usuario_id'], $row['meme_id'], $row['contenido'], $row['fecha_comentario']);

                $comentarios[] = $comentario->getArray();
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['comentarios'] = $comentarios;
            
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Comentario creado");
            $response->setData($returnData);
            $response->send();
            exit();


        }
        catch (ComentarioException $e) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        }
        catch (PDOException $e){
            error_log("Error en BD - " . $e);

            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error en creación de comentarios");
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
else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Ruta no encontrada");
    $response->send();
    exit();
}

?>