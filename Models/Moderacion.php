<?php
    class ModeracionException extends Exception {}

    class Moderacion {
        private $_id;
        private $_meme_id;
        private $_estatus_moderacion;
        private $_retroalimentacion;
        private $_fecha_solicitud;

        public function __construct($id, $memeId, $estatusMod, $retroalimentacion, $fechaSolicitud){
            $this->setID($id);
            $this->setMemeID($memeId);
            $this->setEstatusModeracion($estatusMod);
            $this->setRetroalimentacion($retroalimentacion);
            $this->setFechaSolicitud($fechaSolicitud);
        }


        public function getID() {
            return $this->_id;
        }
    
        public function getMemeID() {
            return $this->_meme_id;
        }

        public function getEstatusModeracion() {
            return $this->_estatus_moderacion;
        }

        public function getRetroalimentacion() {
            return $this->_retroalimentacion;
        }

        public function getFechaSolicitud() {
            return $this->_fecha_solicitud;
        }



        public function setID($id) {
            if ($id !== null && (!is_numeric($id) || $id <= 0 || $id >= 2147483647 || $this->_id !== null)) {
                throw new ModeracionException("Error en ID de moderación");
            }
            $this->_id = $id;
        }
    
        public function setMemeID($memeId) {
            if (!is_numeric($memeId) || $memeId <= 0 || $memeId >= 2147483647) {
                throw new ModeracionException("Error en ID de meme de moderación");
            }
            $this->_meme_id = $memeId;
        }

        public function setEstatusModeracion($estatusMod) {
            if (strtoupper($estatusMod) !== 'ACEPTADO' && strtoupper($estatusMod) !== 'RECHAZADO' && strtoupper($estatusMod) !== 'PENDIENTE') {
                throw new ModeracionException("Error en estatus de moderación");
            }
            $this->_estatus_moderacion = $estatusMod;
        }

        public function setRetroalimentacion($retroalimentacion) {
            if ($retroalimentacion !== null && strlen($retroalimentacion) > 250 || strlen($retroalimentacion) < 1) {
                throw new ModeracionException("Error en retroalimentación de moderación");
            }
            $this->_retroalimentacion = $retroalimentacion;
        }

        public function setFechaSolicitud($fechaSolicitud) {
            if ($fechaSolicitud !== null && date_format(date_create_from_format('Y-m-d H:i', $fechaSolicitud), 'Y-m-d H:i') !== $fechaSolicitud) {
                throw new ModeracionException("Error en fecha de solicitud de moderación");
            }
            $this->_fecha_solicitud = $fechaSolicitud;
        }

        public function getArray() {
            $moderacion = array();
    
            $moderacion['id'] = $this->getID();
            $moderacion['meme_id'] = $this->getMemeID();
            $moderacion['estatus_moderacion'] = $this->getEstatusModeracion();
            $moderacion['retroalimentacion'] = $this->getRetroalimentacion();
            $moderacion['fecha_solicitud'] = $this->getFechaSolicitud();
    
            return $moderacion;
        }
    }
?>