<?php

require_once('../Models/BD.php');
require_once('../Models/MemeTag.php');
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

if (array_key_exists("meme_id", $_GET)) {
    // Devuelve todas las relaciones del meme_id indicado
    //GET host/meme_tag/meme_id={meme_id}
    if($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        $meme_id = $_GET['meme_id'];
        if ($meme_id == '' || !is_numeric($meme_id)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El id de meme no puede estar vacío y debe ser numérico");
            $response->send();
            exit();
        }

        try {
            $sql = 'SELECT meme_id, tag_id
                    FROM memes_tags WHERE meme_id = :meme_id';
            $query = $connection->prepare($sql);
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);

            $query->execute();

            $rowCount = $query->rowCount();
            $memes_tags = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme_tag = new MemeTag($row['meme_id'], $row['tag_id']);

                $memes_tags[] = $meme_tag->getArray();
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes_tags'] = $memes_tags;
            
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setToCache(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (MemeTagException $e){
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
else if (empty($_GET)) {
    //POST host/memes_tags
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
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

            if (!isset($json_data->meme_id) || !isset($json_data->tag_id)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->meme_id) ? $response->addMessage('El id del meme es obligatorio') : false);
                (!isset($json_data->tag_id) ? $response->addMessage('El id del tag es obligatorio') : false);
                $response->send();
                exit();
            }

            $meme_tag = new MemeTag(
                $json_data->meme_id,
                $json_data->tag_id
            );

            $meme_id = $meme_tag->getMemeID();
            $tag_id = $meme_tag->getTagID();
            
            $query = $connection->prepare('INSERT INTO memes_tags (meme_id, tag_id) VALUES (:meme_id, :tag_id)');
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al crear el enlace meme-tag");
                $response->send();
                exit();
            }

            $query = $connection->prepare('SELECT * FROM memes_tags WHERE meme_id = :meme_id AND tag_id = :tag_id');
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al obtener el enlace meme-tag después de crearlo");
                $response->send();
                exit();
            }

            $memes_tags = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $meme_tag = new MemeTag($row['meme_id'], $row['tag_id']);

                $memes_tags[] = $meme_tag->getArray();
            }

            $returnData = array();
            $returnData['total_registros'] = $rowCount;
            $returnData['memes_tags'] = $memes_tags;
            
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Enlace meme-tag creado");
            $response->setData($returnData);
            $response->send();
            exit();

        }
        catch (MemeTagException $e) {
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
            $response->addMessage("Error en creación de meme-tag");
            $response->send();
            exit();
        }

    }
    else if($_SERVER['REQUEST_METHOD'] === 'DELETE')
    {
        try{
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

            if (!isset($json_data->meme_id) || !isset($json_data->tag_id)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->meme_id) ? $response->addMessage('El id del meme es obligatorio') : false);
                (!isset($json_data->tag_id) ? $response->addMessage('El id del tag es obligatorio') : false);
                $response->send();
                exit();
            }

            $meme_tag = new MemeTag(
                $json_data->meme_id,
                $json_data->tag_id
            );

            $meme_id = $meme_tag->getMemeID();
            $tag_id = $meme_tag->getTagID();
            
            $query = $connection->prepare('DELETE FROM memes_tags WHERE meme_id = :meme_id AND tag_id = :tag_id');
            $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
            $query->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Enlace meme-tag no encontrado");
                $response->send();
                exit();
            }

            $response = new Response();
        
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Enlace meme-tag eliminado");
            $response->send();
            exit();
        }
        catch (PDOException $e){
            error_log("Error en BD - " . $e);

            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error en eliminación de meme-tag");
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