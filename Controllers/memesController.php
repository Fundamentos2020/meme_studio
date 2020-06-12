<?php

require_once('../Models/BD.php');
require_once('../Models/Meme.php');
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

    

// GET
// host/memes/populares="diario"
// host/memes/populares="semanal"
// host/memes/populares="mensual
// host/memes/populares="todos"
if(array_key_exists("populares", $_GET)){
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        // Devolver en orden de likes en un cierto rango de tiempo
        $rangoTiempo = $_GET['populares'];

        if($rangoTiempo !== 'diario' &&  $rangoTiempo !== 'semanal' &&
            $rangoTiempo !== 'mensual' && $rangoTiempo !== 'todos')
        {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El valor de populares no es correcto");
            $response->send();
            exit();
        }

        try {
            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE estado_meme = \'ACEPTADO\'';
            if($rangoTiempo === 'diario')
                $sql .= ' AND DATEDIFF(CURDATE(), DATE(fecha_publicacion)) <= 1';
            if($rangoTiempo === 'semanal')
                $sql .= ' AND DATEDIFF(CURDATE(), DATE(fecha_publicacion)) <= 7';
            if($rangoTiempo === 'mensual')
                $sql .= ' AND DATEDIFF(CURDATE(), DATE(fecha_publicacion)) <= 30';
            $sql .= ' ORDER BY likes DESC';

            $query = $connection->prepare($sql);
            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            $rows_memes = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows_memes as $row_meme) {
                $meme = new Meme($row_meme['meme_id'], $row_meme['usuario_id'], $row_meme['likes'], $row_meme['dislikes'], 
                        $row_meme['estado_meme'], $row_meme['ruta_imagen_meme'], $row_meme['titulo'], 
                        $row_meme['texto_superior'], $row_meme['texto_inferior'], $row_meme['fecha_creacion'], 
                        $row_meme['fecha_publicacion']);
                $meme_completo = $meme->getArray();


                $query = $connection->prepare('SELECT nombre_usuario FROM usuarios WHERE usuario_id =:id');
                $query->bindParam(':id', $row_meme['usuario_id'], PDO::PARAM_INT);
                $query->execute();
                $meme_completo['nombre_usuario'] = $query->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

                $sql = 'SELECT tags.nombre_tag FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.meme_id =:meme_id AND memes.meme_id = memes_tags.meme_id AND memes_tags.tag_id = tags.tag_id';
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_completo['meme_id'], PDO::PARAM_INT);
                $query->execute();

                $tags = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tags[] = $row['nombre_tag'];
                }
                $meme_completo['tags'] = $tags;

                $memes[] = $meme_completo;
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeException $e){
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
else if(array_key_exists("usuario_id", $_GET)){
    // host/memes/usuario_id={id}
    // Devuelve todos los memes del usuario en cuestion
    if($_SERVER['REQUEST_METHOD'] === 'GET'){

        $usuario_id = $_GET['usuario_id'];
        if ($usuario_id == '' || !is_numeric($usuario_id)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El id de usuario no puede estar vacío y debe ser numérico");
            $response->send();
            exit();
        }


        try {
            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC';
            $query = $connection->prepare($sql);
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            $rows_memes = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows_memes as $row_meme) {
                $meme = new Meme($row_meme['meme_id'], $row_meme['usuario_id'], $row_meme['likes'], $row_meme['dislikes'], 
                        $row_meme['estado_meme'], $row_meme['ruta_imagen_meme'], $row_meme['titulo'], 
                        $row_meme['texto_superior'], $row_meme['texto_inferior'], $row_meme['fecha_creacion'], 
                        $row_meme['fecha_publicacion']);
                $meme_completo = $meme->getArray();


                $query = $connection->prepare('SELECT nombre_usuario FROM usuarios WHERE usuario_id =:id');
                $query->bindParam(':id', $row_meme['usuario_id'], PDO::PARAM_INT);
                $query->execute();
                $meme_completo['nombre_usuario'] = $query->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

                $sql = 'SELECT tags.nombre_tag FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.meme_id =:meme_id AND memes.meme_id = memes_tags.meme_id AND memes_tags.tag_id = tags.tag_id';
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_completo['meme_id'], PDO::PARAM_INT);
                $query->execute();

                $tags = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tags[] = $row['nombre_tag'];
                }
                $meme_completo['tags'] = $tags;

                $memes[] = $meme_completo;
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeException $e){
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
}
else if(array_key_exists("tag", $_GET)){

    // host/memes/tag={tag}
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $nombre_tag = $_GET['tag'];

        if($nombre_tag === '')
        {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El tag no puede estar vacío");
            $response->send();
            exit();
        }

        try {
            $sql = 'SELECT memes.meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                        FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.estado_meme = \'ACEPTADO\' AND memes.meme_id = memes_tags.meme_id AND memes_tags.tag_id = tags.tag_id 
                        AND tags.nombre_tag = :nombre_tag ORDER BY fecha_publicacion DESC';
            $query = $connection->prepare($sql);
            $query->bindParam(':nombre_tag', $nombre_tag, PDO::PARAM_INT);

            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            $rows_memes = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows_memes as $row_meme) {
                $meme = new Meme($row_meme['meme_id'], $row_meme['usuario_id'], $row_meme['likes'], $row_meme['dislikes'], 
                        $row_meme['estado_meme'], $row_meme['ruta_imagen_meme'], $row_meme['titulo'], 
                        $row_meme['texto_superior'], $row_meme['texto_inferior'], $row_meme['fecha_creacion'], 
                        $row_meme['fecha_publicacion']);
                $meme_completo = $meme->getArray();


                $query = $connection->prepare('SELECT nombre_usuario FROM usuarios WHERE usuario_id =:id');
                $query->bindParam(':id', $row_meme['usuario_id'], PDO::PARAM_INT);
                $query->execute();
                $meme_completo['nombre_usuario'] = $query->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

                $sql = 'SELECT tags.nombre_tag FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.meme_id =:meme_id AND memes.meme_id = memes_tags.meme_id AND memes_tags.tag_id = tags.tag_id';
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_completo['meme_id'], PDO::PARAM_INT);
                $query->execute();

                $tags = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tags[] = $row['nombre_tag'];
                }
                $meme_completo['tags'] = $tags;

                $memes[] = $meme_completo;
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeException $e){
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
else if(array_key_exists("meme_id", $_GET)){
    $meme_id = $_GET['meme_id'];

    if ($meme_id == '' || !is_numeric($meme_id)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El id del meme no puede estar vacío y debe ser numérico");
        $response->send();
        exit();
    }

    // GET
    // host/memes/meme_id={id}
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        try {
            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE meme_id = :meme_id';
            $query = $connection->prepare($sql);
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No se encontró el meme");
                $response->send();
                exit();
            }
            
            $rows_memes = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows_memes as $row_meme) {
                $meme = new Meme($row_meme['meme_id'], $row_meme['usuario_id'], $row_meme['likes'], $row_meme['dislikes'], 
                        $row_meme['estado_meme'], $row_meme['ruta_imagen_meme'], $row_meme['titulo'], 
                        $row_meme['texto_superior'], $row_meme['texto_inferior'], $row_meme['fecha_creacion'], 
                        $row_meme['fecha_publicacion']);
                $meme_completo = $meme->getArray();


                $query = $connection->prepare('SELECT nombre_usuario FROM usuarios WHERE usuario_id =:id');
                $query->bindParam(':id', $row_meme['usuario_id'], PDO::PARAM_INT);
                $query->execute();
                $meme_completo['nombre_usuario'] = $query->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

                $sql = 'SELECT tags.nombre_tag FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.meme_id =:meme_id AND memes.meme_id = memes_tags.meme_id AND memes_tags.tag_id = tags.tag_id';
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_completo['meme_id'], PDO::PARAM_INT);
                $query->execute();

                $tags = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tags[] = $row['nombre_tag'];
                }
                $meme_completo['tags'] = $tags;

                $memes[] = $meme_completo;
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeException $e) {
            $response = new Response();
        
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        }
        catch (PDOException $e) {
            error_log("Error en DB - " . $e, 0);
        
            $response = new Response();
        
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al obtener meme");
            $response->send();
        }
    }
    // DELETE
    // host/memes/meme_id={id}
    else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        try{
            $query = $connection->prepare('DELETE FROM memes WHERE id_meme=:id_meme');
            $query->bindParam('meme_id', $meme_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0){
                $response = new Response();
        
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Meme no encontrado");
                $response->send();
                exit();
            }

            $response = new Response();
        
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Tarea eliminada");
            $response->send();
            exit();
        }
        catch (PDOException $e) {
            error_log("Error en DB - " . $e, 0);
        
            $response = new Response();
        
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al eliminar meme");
            $response->send();
        }
    }
    // PATCH
    // host/memes/meme_id={id}
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

            $campos_query = "";

            $actualiza_likes = false;
            $actualiza_dislikes = false;
            $actualiza_estado_meme = false;
            $actualiza_rutaImagenMeme = false;
            $actualiza_titulo = false;
            $actualiza_textoSuperior = false;
            $actualiza_textoInferior = false;
            $actualiza_fechaCreacion = false;
            $actualiza_fechaPublicacion = false;

            if (isset($json_data->likes)) {
                $actualiza_likes = true;
                $campos_query .= "likes = :likes, ";
            }
            if (isset($json_data->dislikes)) {
                $actualiza_dislikes = true;
                $campos_query .= "dislikes = :dislikes, ";
            }
            if (isset($json_data->estado_meme)) {
                $actualiza_estado_meme = true;
                $campos_query .= "estado_meme = :estado_meme, ";
            }
            if (isset($json_data->ruta_imagen_meme)) {
                $actualiza_rutaImagenMeme = true;
                $campos_query .= "ruta_imagen_meme = :ruta_imagen_meme, ";
            }
            if (isset($json_data->titulo)) {
                $actualiza_titulo = true;
                $campos_query .= "titulo = :titulo, ";
            }
            if (isset($json_data->texto_superior)) {
                $actualiza_textoSuperior = true;
                $campos_query .= "texto_superior = :texto_superior, ";
            }
            if (isset($json_data->texto_inferior)) {
                $actualiza_textoInferior = true;
                $campos_query .= "texto_inferior = :texto_inferior, ";
            }
            if (isset($json_data->fecha_creacion)) {
                $actualiza_fechaCreacion = true;
                $campos_query .= "fecha_creacion = :fecha_creacion, ";
            }
            if (isset($json_data->fecha_publicacion)) {
                $actualiza_fechaPublicacion = true;
                $campos_query .= "fecha_publicacion = :fecha_publicacion, ";
            }

            $campos_query = rtrim($campos_query, ", ");

            if ($actualiza_likes === false && $actualiza_dislikes === false && $actualiza_estado_meme === false &&
                $actualiza_rutaImagenMeme === false && $actualiza_titulo === false && $actualiza_textoSuperior === false && $actualiza_textoInferior === false &&
                $actualiza_fechaCreacion === false && $actualiza_fechaPublicacion === false) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("No hay campos para actualizar");
                $response->addMessage($json_data);
                $response->send();
                exit();
            }

            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE meme_id = :meme_id';
            $query = $connection->prepare($sql);
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No se encontró el meme a editar");
                $response->send();
                exit();
            }

            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['meme_id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
                        $row['estado_meme'], $row['ruta_imagen_meme'], $row['titulo'], 
                        $row['texto_superior'], $row['texto_inferior'], $row['fecha_creacion'], 
                        $row['fecha_publicacion']);
            }

            $cadena_query = 'UPDATE memes SET ' . $campos_query . ' WHERE meme_id =:meme_id';
            $query = $connection->prepare($cadena_query);

            if($actualiza_likes) {
                $meme->setLikes($json_data->likes);
                $up_likes = $meme->getLikes();
                $query->bindParam(':likes', $up_likes, PDO::PARAM_INT);
            }
            if($actualiza_dislikes) {
                $meme->setDislikes($json_data->dislikes);
                $up_dislikes = $meme->getDislikes();
                $query->bindParam(':dislikes', $up_dislikes, PDO::PARAM_INT);
            }
            if($actualiza_estado_meme) {
                $meme->setEstadoMeme($json_data->estado_meme);
                $up_estadoMeme = $meme->getEstadoMeme();
                $query->bindParam(':estado_meme', $up_estadoMeme, PDO::PARAM_STR);
            }
            if($actualiza_rutaImagenMeme) {
                $meme->setRutaImagenMeme($json_data->ruta_imagen_meme);
                $up_rutaImagenMeme = $meme->getRutaImagenMeme();
                $query->bindParam(':ruta_imagen_meme', $up_rutaImagenMeme, PDO::PARAM_STR);
            }
            if($actualiza_titulo) {
                $meme->setTitulo($json_data->titulo);
                $up_titulo = $meme->getTitulo();
                $query->bindParam(':titulo', $up_titulo, PDO::PARAM_STR);
            }
            if($actualiza_textoSuperior) {
                $meme->setTextoSuperior($json_data->texto_superior);
                $up_textoSuperior = $meme->getTextoSuperior();
                $query->bindParam(':texto_superior', $up_textoSuperior, PDO::PARAM_STR);
            }
            if($actualiza_textoInferior) {
                $meme->setTextoInferior($json_data->texto_inferior);
                $up_textoInferior = $meme->getTextoInferior();
                $query->bindParam(':texto_inferior', $up_textoInferior, PDO::PARAM_STR);
            }
            if($actualiza_fechaCreacion) {
                $meme->setFechaCreacion($json_data->fecha_creacion);
                $up_fechaCreacion = $meme->getFechaCreacion();
                $query->bindParam(':fecha_creacion', $up_fechaCreacion, PDO::PARAM_STR);
            }
            if($actualiza_fechaPublicacion) {
                $meme->setFechaPublicacion($json_data->fecha_publicacion);
                $up_fechaPublicacion = $meme->getFechaPublicacion();
                $query->bindParam(':fecha_publicacion', $up_fechaPublicacion, PDO::PARAM_STR);
            }

            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al actualizar el meme");
                $response->send();
                exit();
            }

            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE meme_id = :meme_id';
            $query = $connection->prepare($sql);
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No se encontró el meme después de actualizar");
                $response->send();
                exit();
            }

            $memes = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['meme_id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
                        $row['estado_meme'], $row['ruta_imagen_meme'], $row['titulo'], 
                        $row['texto_superior'], $row['texto_inferior'], $row['fecha_creacion'], 
                        $row['fecha_publicacion']);
                
                $memes[] = $meme->getArray();
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;

            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Meme actualizado");
            $response->setData($returnData);
            $response->send();
            exit();

        }
        catch(MemeException $e) {
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
            $response->addMessage("Error en BD al actualizar el meme");
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
else if(empty($_GET)){
    // Devuelve todos los memes en orden de publicacion (primero el más reciente)
    // host/memes/
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        try {
            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE estado_meme = \'ACEPTADO\' ORDER BY fecha_publicacion DESC';
            $query = $connection->prepare($sql);
            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            $rows_memes = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows_memes as $row_meme) {
                $meme = new Meme($row_meme['meme_id'], $row_meme['usuario_id'], $row_meme['likes'], $row_meme['dislikes'], 
                        $row_meme['estado_meme'], $row_meme['ruta_imagen_meme'], $row_meme['titulo'], 
                        $row_meme['texto_superior'], $row_meme['texto_inferior'], $row_meme['fecha_creacion'], 
                        $row_meme['fecha_publicacion']);
                $meme_completo = $meme->getArray();


                $query = $connection->prepare('SELECT nombre_usuario FROM usuarios WHERE usuario_id =:id');
                $query->bindParam(':id', $row_meme['usuario_id'], PDO::PARAM_INT);
                $query->execute();
                $meme_completo['nombre_usuario'] = $query->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

                $sql = 'SELECT tags.nombre_tag FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.meme_id =:meme_id AND memes.meme_id = memes_tags.meme_id AND memes_tags.tag_id = tags.tag_id';
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_completo['meme_id'], PDO::PARAM_INT);
                $query->execute();

                $tags = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tags[] = $row['nombre_tag'];
                }
                $meme_completo['tags'] = $tags;

                $memes[] = $meme_completo;
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeException $e){
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
    // Crea un nuevo meme
    else if($_SERVER['REQUEST_METHOD'] === 'POST'){
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

            if (!isset($json_data->usuario_id) || !isset($json_data->ruta_imagen_meme) || !isset($json_data->titulo)  || !isset($json_data->fecha_creacion)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->usuario_id) ? $response->addMessage('El id de usuario es obligatorio') : false);
                (!isset($json_data->ruta_imagen_meme) ? $response->addMessage('La ruta de la imagen es obligatoria') : false);
                (!isset($json_data->titulo) ? $response->addMessage('El título es obligatorio') : false);
                (!isset($json_data->fecha_creacion) ? $response->addMessage('La fecha de creacion es obligatoria') : false);
                $response->send();
                exit();
            }

            $meme = new Meme(
                null,
                $json_data->usuario_id,
                (isset($json_data->likes) ? $json_data->likes : null),
                (isset($json_data->dislikes) ? $json_data->dislikes : null),
                (isset($json_data->estado_meme) ? $json_data->estado_meme : null),
                $json_data->ruta_imagen_meme,
                $json_data->titulo,
                (isset($json_data->texto_superior) ? $json_data->texto_superior : null),
                (isset($json_data->texto_inferior) ? $json_data->texto_inferior : null),
                $json_data->fecha_creacion,
                (isset($json_data->fecha_publicacion) ? $json_data->fecha_publicacion : null)
            );

            $usuario_id = $meme->getUsuarioID();
            $likes = $meme->getLikes();
            $dislikes = $meme->getDislikes();
            $estado_meme = $meme->getEstadoMeme();
            $ruta_imagen_meme = $meme->getRutaImagenMeme();
            $titulo = $meme->getTitulo();
            $texto_superior = $meme->getTextoSuperior();
            $texto_inferior = $meme->getTextoInferior();
            $fecha_creacion = $meme->getFechaCreacion();
            $fecha_publicacion = $meme->getFechaPublicacion();

            $sql = 'INSERT INTO memes (usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, 
                        titulo, texto_superior, texto_inferior, fecha_creacion, fecha_publicacion)
                    VALUES (:usuario_id, :likes, :dislikes, :estado_meme, :ruta_imagen_meme, :titulo,
                        :texto_superior, :texto_inferior,  STR_TO_DATE(:fecha_creacion, \'%Y-%m-%d %H:%i\'),
                        STR_TO_DATE(:fecha_publicacion, \'%Y-%m-%d %H:%i\'))';
            $query = $connection->prepare($sql);
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $query->bindParam(':likes', $likes, PDO::PARAM_INT);
            $query->bindParam(':dislikes', $dislikes, PDO::PARAM_INT);
            $query->bindParam(':estado_meme', $estado_meme, PDO::PARAM_STR);
            $query->bindParam(':ruta_imagen_meme', $ruta_imagen_meme, PDO::PARAM_STR);
            $query->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $query->bindParam(':texto_superior', $texto_superior, PDO::PARAM_STR);
            $query->bindParam(':texto_inferior', $texto_inferior, PDO::PARAM_STR);
            $query->bindParam(':fecha_creacion', $fecha_creacion, PDO::PARAM_STR);
            $query->bindParam(':fecha_publicacion', $fecha_publicacion, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al crear el meme");
                $response->send();
                exit();
            }

            $ultimo_ID = $connection->lastInsertId();

            $sql = 'SELECT meme_id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE meme_id = :id';
            $query = $connection->prepare($sql);
            $query->bindParam(':id', $ultimo_ID, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Error al obtener el meme después de crearlo");
                $response->send();
                exit();
            }

            $memes = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['meme_id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
                        $row['estado_meme'], $row['ruta_imagen_meme'], $row['titulo'], 
                        $row['texto_superior'], $row['texto_inferior'], $row['fecha_creacion'], 
                        $row['fecha_publicacion']);
                
    
                $memes[] = $meme->getArray();
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes'] = $memes;

            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Meme creado");
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeException $e) {
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
            $response->addMessage("Error en creación del meme");
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
else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Ruta no encontrada");
    $response->send();
    exit();
}


?>