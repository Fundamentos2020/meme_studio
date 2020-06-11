<?php
    class UsuarioException extends Exception {}

    class Usuario {
        private $_usuario_id;
        private $_nombre_completo;
        private $_nombre_usuario;
        private $_contrasena;
        private $_rol;
        private $_email;
        private $_ruta_imagen_perfil;
        private $_descripcion;


        public function __construct($id, $rolUsuario, $nombreCompleto, $nombreUsuario, $contra, $emailUsuario, $rutaPP, $descrip) {
            $this->setID($id);
            $this->setRol($rolUsuario);
            $this->setNombreCompleto($nombreCompleto);
            $this->setNombreUsuario($nombreUsuario);
            $this->setContrasena($contra);
            $this->setEmail($emailUsuario);
            $this->setRutaImagenPerfil($rutaPP);
            $this->setDescripcion($descrip);
        }



        public function getID() {
            return $this->_usuario_id;
        }
    
        public function getNombreCompleto() {
            return $this->_nombre_completo;
        }

        public function getNombreUsuario() {
            return $this->_nombre_usuario;
        }

        public function getContrasena() {
            return $this->_contrasena;
        }

        public function getRol(){
            return $this->_rol;
        }

        public function getEmail(){
            return $this->_email;
        }

        public function getRutaImagenPerfil(){
            return $this->_ruta_imagen_perfil;
        }

        public function getDescripcion(){
            return $this->_descripcion;
        }


        public function setID($id) {
            if ($id !== null && (!is_numeric($id) || $id <= 0 || $id >= 2147483647 || $this->_usuario_id !== null)) {
                throw new UsuarioException("Error en ID de usuario");
            }
            $this->_usuario_id = $id;
        }
    
        public function setNombreCompleto($nombreCompleto) {
            if ($nombreCompleto !== null && strlen($nombreCompleto) < 5) {
                throw new UsuarioException("Error en el nombre completo de usuario");
            }
            $this->_nombre_completo = $nombreCompleto;
        }

        public function setNombreUsuario($nombreUsuario) {
            if ($nombreUsuario !== null && strlen($nombreUsuario) < 1 || strlen($nombreUsuario) > 20) {
                throw new UsuarioException("Error en el nombre de usuario");
            }
            $this->_nombre_usuario = $nombreUsuario;
        }

        public function setContrasena($contra) {
            if ($contra !== null && (strlen($contra) < 1 || strlen($contra) > 255)) {
                throw new UsuarioException("Error en la contraseña de usuario");
            }
            $this->_contrasena = $contra;
        }

        public function setRol($rolUsuario){
            if (strtoupper($rolUsuario) !== 'USUARIO' && strtoupper($rolUsuario) !== 'MODERADOR') {
                throw new UsuarioException("Error en el rol de usuario");
            }
            $this->_rol = $rolUsuario;
        }

        public function setEmail($emailUsuario){
            if ($emailUsuario !== null && filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
                throw new UsuarioException("Error en el email de usuario");
            }
            $this->_email = $emailUsuario;
        }

        public function setRutaImagenPerfil($rutaPP){
            if ($rutaPP !== null && file_exists($rutaPP)) {
                throw new UsuarioException("Error en la imagen de usuario");
            }
            $this->_ruta_imagen_perfil = $rutaPP;
        }

        public function setDescripcion($descrip){
            if ($descrip !== null && strlen($descrip) > 500) {
                throw new UsuarioException("Error en descripción de usuario");
            }
            $this->_descripcion = $descrip;
        }


        public function getArray() {
            $usuario = array();
    
            $usuario['usuario_id'] = $this->getID();
            $usuario['nombre_completo'] = $this->getNombreCompleto();
            $usuario['nombre_usuario'] = $this->getNombreUsuario();
            $usuario['contrasena'] = $this->getContrasena();
            $usuario['rol'] = $this->getRol();
            $usuario['email'] = $this->getEmail();
            $usuario['ruta_imagen_perfil'] = $this->getRutaImagenPerfil();
            $usuario['descripcion'] = $this->getDescripcion();
    
            return $usuario;
        }
    }
?>