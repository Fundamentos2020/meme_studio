<?php
    class ComentarioException extends Exception {}

    class Comentario {
        private $_id;
        private $_usuario_id;
        private $_meme_id;
        private $_contenido;
        private $_fecha_comentario;

        public function __construct($id, $usuario_id, $meme_id, $contenido, $fecha_comentario){
            $this->setID($id);
            $this->setUsuarioID($usuario_id);
            $this->setMemeID($meme_id);
            $this->setContenido($contenido);
            $this->setFechaComentario($fecha_comentario);
        }

        public function getID(){
            return $this->_id;
        }

        public function getUsuarioID(){
            return $this->_usuario_id;
        }

        public function getMemeID(){
            return $this->_meme_id;
        }

        public function getContenido(){
            return $this->_contenido;
        }
        
        public function getFechaComentario(){
            return $this->_fecha_comentario;
        }



        public function setID($id){
            if ($id !== null && (!is_numeric($id) || $id <= 0 || $id >= 2147483647 || $this->_id !== null)) {
                throw new ComentarioException("Error en ID del Comentario");
            }
            $this->_id = $id;
        }

        public function setUsuarioID($usuario_id){
            if ($usuario_id === null || !is_numeric($usuario_id) || $usuario_id <= 0 || $usuario_id >= 2147483647) {
                throw new ComentarioException("Error en ID de usuario del Comentario");
            }
            $this->_usuario_id = $usuario_id;
        }

        public function setMemeID($meme_id){
            if ($meme_id === null || !is_numeric($meme_id) || $meme_id <= 0 || $meme_id >= 2147483647) {
                throw new ComentarioException("Error en ID de meme del Comentario");
            }
            $this->_meme_id = $meme_id;
        }

        public function setContenido($contenido){
            if ($contenido === null || strlen($contenido) > 512 || strlen($contenido) < 1) {
                throw new ComentarioException("Error en el contenido del Comentario");
            }
            $this->_contenido = $contenido;
        }

        public function setFechaComentario($fecha_comentario){
            if ($fecha_comentario === null ||  date_format(date_create_from_format('Y-m-d H:i', $fecha_comentario), 'Y-m-d H:i') !== $fecha_comentario) {
                throw new ComentarioException("Error en fecha del Comentario");
            }
            $this->_fecha_comentario = $fecha_comentario;
        }

        public function getArray() {
            $comentario = array();

            $comentario['id'] = $this->getID();
            $comentario['usuario_id'] = $this->getUsuarioID();
            $comentario['meme_id'] = $this->getMemeID();
            $comentario['contenido'] = $this->getContenido();
            $comentario['fecha_comentario'] = $this->getFechaComentario();
    
            return $comentario;
        }
    }
?>