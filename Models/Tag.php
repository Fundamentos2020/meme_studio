<?php
    class TagException extends Exception {}

    class Tag {
        private $_tag_id;
        private $_nombre_tag;

        public function __construct($id, $nombreTag){
            $this->setID($id);
            $this->setNombreTag($nombreTag);
        }

        public function getID() {
            return $this->_tag_id;
        }
    
        public function getNombreTag() {
            return $this->_nombre_tag;
        }

        public function setID($id) {
            if ($id !== null && (!is_numeric($id) || $id <= 0 || $id >= 2147483647 || $this->_tag_id !== null)) {
                throw new TagException("Error en ID de tag");
            }
            $this->_tag_id = $id;
        }
    
        public function setNombreTag($nombreTag) {
            if ($nombreTag !== null && (strlen($nombreTag) < 1 || strlen($nombreTag) > 25)  ) {
                throw new TagException("Error en el nombre del tag");
            }
            $this->_nombre_tag = $nombreTag;
        }

        public function getArray() {
            $tag = array();
    
            $tag['tag_id'] = $this->getID();
            $tag['nombre_tag'] = $this->getNombreTag();
    
            return $tag;
        }
    }
?>