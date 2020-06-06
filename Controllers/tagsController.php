<?php
    require_once('../Models/BD.php');
    require_once('../Models/Tag.php');
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

    //GET host/tag
    if(empty($_GET)){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            try {
                $query = $connection->prepare('SELECT * FROM tag');
                $query->execute();
                $tags = array();

                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tag = new Tag($row['id'], $row['nombre_tag']);

                    $tags[] = $tag->getArray();
                }

                $returnData = array();
                $returnData['total_tags'] = $rowCount;
                $returnData['tags'] = $tags;

                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setToCache(true);
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch(TagException $e){
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
                $response->addMessage("Error en consulta de tags");
                $response->send();
                exit();
            }
        }

        elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
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
    
                if (!isset($json_data->nombre_tag)) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    (!isset($json_data->nombre_tag) ? $response->addMessage('El tag es obligatorio') : false);
                    $response->send();
                    exit();
                }
    
                $tag = new Tag(
                    null, 
                    $json_data->nombre_tag,
                );
    
                $nombreTag = $tag->getNombreTag();
    
                $query = $connection->prepare('INSERT INTO tag (nombre_tag) VALUES (:nombre_tag)');
                $query->bindParam(':nombre_tag', $titulo, PDO::PARAM_STR);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al crear el tag");
                    $response->send();
                    exit();
                }
    
                $ultimo_ID = $connection->lastInsertId();
    
                $query = $connection->prepare('SELECT id, nombre_tag FROM tag WHERE id = :id AND nombre_tag = :nombre_tag');
                $query->bindParam(':id', $ultimo_ID, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al obtener tarea después de crearla");
                    $response->send();
                    exit();
                }
    
                $tags = array();
    
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tag = new Tag($row['id'], $row['nombre_tag']);
    
                    $tags[] = $tag->getArray();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['tags'] = $tags;
    
                $response = new Response();
                $response->setHttpStatusCode(201);
                $response->setSuccess(true);
                $response->addMessage("Tags creado");
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch (TareaException $e) {
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
                $response->addMessage("Error en creación de tareas");
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