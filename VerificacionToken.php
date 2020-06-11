<?php
    function verificacionToken(){
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
            $query = $connection->prepare('SELECT id_usuario, caducidad_token_acceso, activo FROM sesiones, usuarios WHERE sesiones.id_usuario = usuarios.id AND token_acceso = :token_acceso');
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
        
            $consulta_idUsuario = $row['id_usuario'];
            $consulta_cadTokenAcceso = $row['caducidad_token_acceso'];
            $consulta_activo = $row['activo'];
        
            if($consulta_activo !== 'SI') {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Cuenta de usuario no activa");
                $response->send();
                exit();
            }
        
            if (strtotime($consulta_cadTokenAcceso) < time()) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Token de acceso ha caducado");
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
        
        return $consulta_idUsuario;
    }

?>