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
            $sql = 'SELECT id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
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
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
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
            $sql = 'SELECT id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC';
            $query = $connection->prepare($sql);
            $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
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

    // host/memes/usuario_id={id}/meme_id={id}

    // host/memes/usuario_id={id}/meme_id={id}
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
            $sql = 'SELECT memes.id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                        FROM memes INNER JOIN memes_tags INNER JOIN tags 
                        WHERE memes.estado_meme = \'ACEPTADO\' AND memes.id = memes_tags.meme_id AND memes_tags.tag_id = tags.id 
                        AND tags.nombre_tag = :nombre_tag ORDER BY fecha_publicacion DESC';
            $query = $connection->prepare($sql);
            $query->bindParam(':nombre_tag', $nombre_tag, PDO::PARAM_INT);

            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
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
else{
    // GET
    // host/memes/
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        try {
            $sql = 'SELECT id, usuario_id, likes, dislikes, estado_meme, ruta_imagen_meme, titulo, 
                        texto_superior, texto_inferior, 
                        DATE_FORMAT(fecha_creacion, "%Y-%m-%d %H:%i") fecha_creacion,
                        DATE_FORMAT(fecha_publicacion, "%Y-%m-%d %H:%i") fecha_publicacion
                    FROM memes WHERE estado_meme = \'ACEPTADO\' ORDER BY fecha_publicacion DESC';
            $query = $connection->prepare($sql);
            $query->execute();

            $rowCount = $query->rowCount();
            $memes = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme = new Meme($row['id'], $row['usuario_id'], $row['likes'], $row['dislikes'], 
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









?>