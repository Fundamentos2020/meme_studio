<?php
    require_once('../Models/BD.php');
    require_once('../Models/Moderacion.php');
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
    date_default_timezone_set("America/Mexico_City");

    function VerificarModerador(){
        global $connection;
        if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
            $response = new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("No se encontró el token de acceso");
            $response->send();
            exit();
        }
        
        $accesstoken = $_SERVER['HTTP_AUTHORIZATION']; 
        
        try {
            $query = $connection->prepare('SELECT usuarios.usuario_id, caducidad_token_acceso FROM sesiones, usuarios WHERE sesiones.usuario_id = usuarios.usuario_id AND token_acceso = :token_acceso');
            $query->bindParam(':token_acceso', $accesstoken, PDO::PARAM_STR);
            $query->execute();
        
            $rowCount = $query->rowCount();
        
            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Token de acceso no válido");
                $response->send();
                exit();
            }
        
            $row = $query->fetch(PDO::FETCH_ASSOC);
        
            $consulta_idUsuario = $row['usuario_id'];
            $consulta_cadTokenAcceso = $row['caducidad_token_acceso'];
            if (strtotime($consulta_cadTokenAcceso) < time()) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Token de acceso ha caducado");
                $response->addMessage($consulta_cadTokenAcceso . " < " . date('Y-m-d H:i', time()));
                $response->send();
                exit();
            }
    
            $query = $connection->prepare('SELECT usuario_id, rol FROM usuarios WHERE usuario_id = :usuario_id AND rol = \'MODERADOR\'');
            $query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_STR);
            $query->execute();
    
            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("El usuario no tiene permisos de Moderador");
                $response->send();
                exit();
            }
        } 
        catch (PDOException $e) {
            error_log('Error en DB - ' . $e);
        
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al autenticar usuario");
            $response->send();
            exit();
        }
    }

    if(array_key_exists("moderacion_id", $_GET)){
        $moderacion_id = $_GET['moderacion_id'];
    
        if ($moderacion_id == '' || !is_numeric($moderacion_id)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El id de la moderacion no puede estar vacío y debe ser numérico");
            $response->send();
            exit();
        }
        if($_SERVER['REQUEST_METHOD'] === 'PATCH'){
            VerificarModerador();
            
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
        
                $actualiza_retroalimentacion = false;
                $actualiza_estado = false;
                $actualiza_fecha_solicitud = false;
        
                $campos_query = "";
        
                if (isset($json_data->retroalimentacion)) {
                    $actualiza_retroalimentacion = true;
                    $campos_query .= "retroalimentacion = :retroalimentacion, ";
                }
        
                if (isset($json_data->estatus_moderacion)) {
                    $actualiza_estado = true;
                    $campos_query .= "estatus_moderacion = :estatus_moderacion, ";
                }
    
                if (isset($json_data->fecha_solicitud)) {
                    $actualiza_fecha_solicitud = true;
                    $campos_query .= "fecha_solicitud = :fecha_solicitud, ";
                }
        
                $campos_query = rtrim($campos_query, ", ");
        
                if ($actualiza_retroalimentacion === false && $actualiza_estado === false && $actualiza_fecha_solicitud === false) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage("No hay campos para actualizar");
                    $response->send();
                    exit();
                }
        
                $query = $connection->prepare('SELECT moderacion_id, meme_id, estatus_moderacion, retroalimentacion,  DATE_FORMAT(fecha_solicitud, "%Y-%m-%d %H:%i") fecha_solicitud 
                FROM moderaciones WHERE moderacion_id = :moderacion_id');
                $query->bindParam(':moderacion_id', $moderacion_id, PDO::PARAM_INT);
                $query->execute();
        
                $rowCount = $query->rowCount();
        
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el meme ha moderar");
                    $response->send();
                    exit();
                }
        
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $moderar = new Moderacion($row['moderacion_id'], $row['meme_id'], $row['estatus_moderacion'], $row['retroalimentacion'], $row['fecha_solicitud']);
                }
        
                $cadena_query = 'UPDATE moderaciones SET ' . $campos_query . ' WHERE moderacion_id = :moderacion_id';
                $query = $connection->prepare($cadena_query);
        
                if($actualiza_retroalimentacion === true) {
                    $moderar->setRetroalimentacion($json_data->retroalimentacion);
                    $up_retroalimentacion = $moderar->getRetroalimentacion();
                    $query->bindParam(':retroalimentacion', $up_retroalimentacion, PDO::PARAM_STR);
                }
        
                if($actualiza_estado === true) {
                    $moderar->setEstatusModeracion($json_data->estatus_moderacion);
                    $up_estatus = $moderar->getEstatusModeracion();
                    $query->bindParam(':estatus_moderacion', $up_estatus, PDO::PARAM_STR);
                }
    
                if($actualiza_fecha_solicitud === true) {
                    $moderar->setFechaSolicitud($json_data->fecha_solicitud);
                    $up_fechaS = $moderar->getFechaSolicitud();
                    $query->bindParam(':fecha_solicitud', $up_fechaS, PDO::PARAM_STR);
                }
        
                $query->bindParam(':moderacion_id', $moderacion_id, PDO::PARAM_INT);
                $query->execute();
        
                $rowCount = $query->rowCount();
        
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al actualizar moderación");
                    $response->send();
                    exit();
                }
        
                $query = $connection->prepare('SELECT moderacion_id, meme_id, estatus_moderacion, retroalimentacion,  DATE_FORMAT(fecha_solicitud, "%Y-%m-%d %H:%i") fecha_solicitud 
                FROM moderaciones WHERE moderacion_id = :moderacion_id');
                $query->bindParam(':moderacion_id', $moderacion_id, PDO::PARAM_INT);
                $query->execute();
        
                $rowCount = $query->rowCount();
        
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró la moderación después de actulizar");
                    $response->send();
                    exit();
                }
        
                $moderaciones = array();
        
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $moderarr = new Moderacion($row['moderacion_id'], $row['meme_id'], $row['estatus_moderacion'], $row['retroalimentacion'], $row['fecha_solicitud']);
        
                    $moderaciones[] = $moderarr->getArray();
                }
        
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['moderaciones'] = $moderaciones;
        
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage("Moderación actualizada");
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch(ModeracionException $e) {
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
                $response->addMessage("Error en BD al actualizar la moderación");
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
                $sql = 'SELECT moderacion_id, meme_id, estatus_moderacion, retroalimentacion, DATE_FORMAT(fecha_solicitud, "%Y-%m-%d %H:%i") fecha_solicitud
                        FROM moderaciones WHERE estatus_moderacion = \'PENDIENTE\' AND meme_id = :meme_id ORDER BY fecha_solicitud ASC';
    
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
    
                $query->execute();
    
                $rowCount = $query->rowCount();
                $pendientes = array();
                
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $pendiente = new Moderacion($row['moderacion_id'], $row['meme_id'], $row['estatus_moderacion'], $row['retroalimentacion'], $row['fecha_solicitud']);
    
                    $pendientes[] = $pendiente->getArray();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['pendientes'] = $pendientes;
                
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setToCache(true);
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch (ModeracionException $e){
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
    else if (empty($_GET)){
        // GET host/moderaciones
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            VerificarModerador();
            try {
                $sql = 'SELECT moderacion_id, meme_id, estatus_moderacion, retroalimentacion, DATE_FORMAT(fecha_solicitud, "%Y-%m-%d %H:%i") fecha_solicitud
                        FROM moderaciones WHERE estatus_moderacion =  \'PENDIENTE\' ORDER BY fecha_solicitud ASC';
    
                $query = $connection->prepare($sql);
                $query->execute();
    
                $rowCount = $query->rowCount();

                $pendientes = array();
                
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $pendiente = new Moderacion($row['moderacion_id'], $row['meme_id'], $row['estatus_moderacion'], $row['retroalimentacion'], $row['fecha_solicitud']);
    
                    $pendientes[] = $pendiente->getArray();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['pendientes'] = $pendientes;
                
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch (ModeracionException $e){
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
        // POST host/moderaciones
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
    
                if (!isset($json_data->meme_id) || !isset($json_data->estatus_moderacion) || !isset($json_data->retroalimentacion) || !isset($json_data->fecha_solicitud)) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    (!isset($json_data->meme_id) ? $response->addMessage('El id de meme es obligatorio') : false);
                    (!isset($json_data->estatus_moderacion) ? $response->addMessage('El estatus de meme es obligatorio') : false);
                    (!isset($json_data->retroalimentacion) ? $response->addMessage('La retroalimentacion es obligatoria') : false);
                    (!isset($json_data->fecha_solicitud) ? $response->addMessage('La fecha de solicitud es obligatoria') : false);
                    $response->send();
                    exit();
                }
    
                $modera = new Moderacion(
                    null,
                    $json_data->meme_id,
                    $json_data->estatus_moderacion,
                    $json_data->retroalimentacion,
                    $json_data->fecha_solicitud
                );
    
                $meme_id = $modera->getMemeID();
                $estatus_moderacion = $modera->getEstatusModeracion();
                $retroalimentacion = $modera->getRetroalimentacion();
                $fecha_solicitud = $modera->getFechaSolicitud();
    
                $sql = 'INSERT INTO moderaciones (meme_id, estatus_moderacion, retroalimentacion, fecha_solicitud)
                        VALUES (:meme_id, :estatus_moderacion, :retroalimentacion, STR_TO_DATE(:fecha_solicitud, \'%Y-%m-%d %H:%i\'))';
    
                $query = $connection->prepare($sql);
                $query->bindParam(':meme_id', $meme_id, PDO::PARAM_INT);
                $query->bindParam(':estatus_moderacion', $estatus_moderacion, PDO::PARAM_STR);
                $query->bindParam(':retroalimentacion', $retroalimentacion, PDO::PARAM_STR);
                $query->bindParam(':fecha_solicitud', $fecha_solicitud, PDO::PARAM_STR);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al crear la moderacion");
                    $response->send();
                    exit();
                }
    
                $ultimo_ID = $connection->lastInsertId();
    
                $sql = 'SELECT moderacion_id, meme_id, estatus_moderacion, retroalimentacion, DATE_FORMAT(fecha_solicitud, "%Y-%m-%d %H:%i") fecha_solicitud
                        FROM moderaciones WHERE estatus_moderacion = \'PENDIENTE\' AND moderacion_id = :moderacion_id ORDER BY fecha_solicitud ASC';
    
                $query = $connection->prepare($sql);
                $query->bindParam(':moderacion_id', $ultimo_ID, PDO::PARAM_INT);
                $query->execute();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al obtener el la moderacion después de crearla");
                    $response->send();
                    exit();
                }
    
                $moderar = array();
                    
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $modera = new Moderacion($row['moderacion_id'], $row['meme_id'], $row['estatus_moderacion'], $row['retroalimentacion'], $row['fecha_solicitud']);
    
                    $moderar[] = $modera->getArray();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['moderar'] = $moderar;
                
                $response = new Response();
                $response->setHttpStatusCode(201);
                $response->setSuccess(true);
                $response->addMessage("Moderación creada");
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch (ModeracionException $e) {
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
                $response->addMessage("Error en moderación");
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