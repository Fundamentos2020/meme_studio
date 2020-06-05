<?php

class MemeException extends Exception {}

class Meme {
    private $_id;
    private $_usuario_id;
    private $_likes;
    private $_dislikes;
    private $_estado_meme;
    private $_ruta_imagen_meme;
    private $_titulo;
    private $_texto_superior;
    private $_texto_inferior;
    private $_fecha_creacion;
    private $_fecha_publicacion;

    // Constructor
    public function __construct($id, $usuario_id, $likes, $dislikes, $estado_meme,
        $ruta_imagen_meme, $titulo, $texto_superior, $texto_inferior, $fecha_creacion, $fecha_publicacion)
    {
        $this->setID($id);
        $this->setUsuarioID($usuario_id);
        $this->setLikes($likes);
        $this->setDislikes($dislikes);
        $this->setEstadoMeme($estado_meme);
        $this->setRutaImagenMeme($ruta_imagen_meme);
        $this->setTitulo($titulo);
        $this->setTextoSuperior($texto_superior);
        $this->setTextoInferior($texto_inferior);
        $this->setFechaCreacion($fecha_creacion);
        $this->setFechaPublicacion($fecha_publicacion);
    }

    // Funciones de acceso
    public function getID() {
        return $this->_id;
    }

    public function getUsuarioID() {
        return $this->_usuario_id;
    }
    
    public function getLikes() {
        return $this->_likes;
    }

    public function getDislikes() {
        return $this->_dislikes;
    }

    public function getEstadoMeme() {
        return $this->_estado_meme;
    }

    public function getRutaImagenMeme() {
        return $this->_ruta_imagen_meme;
    }

    public function getTitulo() {
        return $this->_titulo;
    }

    public function getTextoSuperior() {
        return $this->_texto_superior;
    }

    public function getTextoInferior() {
        return $this->_texto_inferior;
    }

    public function getFechaCreacion() {
        return $this->_fecha_creacion;
    }

    public function getFechaPublicacion() {
        return $this->_fecha_publicacion;
    }

    
    // Funciones de guardado y comprobación
    public function setID($id) {
        if ($id !== null && (!is_numeric($id) || $id <= 0 || $id >= 2147483647 || $this->_id !== null)) {
            throw new MemeException("Error en ID del meme");
        }
        $this->_id;
    }

    public function setUsuarioID($usuario_id) {
        if ($usuario_id === null || !is_numeric($usuario_id) || $usuario_id <= 0 || $usuario_id >= 2147483647) {
            throw new MemeException("Error en ID de usuario del meme");
        }
        $this->_usuario_id = $usuario_id;
    }
    
    public function setLikes($likes) {
        if ($likes !== null && (!is_numeric($likes) || $likes < 0 || $likes >= 2147483647)) {
            throw new MemeException("Error en likes del meme");
        }
        $this->_likes = $likes;
    }

    public function setDislikes($dislikes) {
        if ($dislikes !== null && (!is_numeric($dislikes) || $dislikes < 0 ||  $dislikes >= 2147483647)) {
            throw new MemeException("Error en likes del meme");
        }
        $this->_dislikes = $dislikes;
    }

    public function setEstadoMeme($estado_meme) {
        if (strtoupper($estado_meme) !== 'PRIVADO' && strtoupper($estado_meme) !== 'PENDIENTE'
        && strtoupper($estado_meme) !== 'RECHAZADO' && strtoupper($estado_meme) !== 'ACEPTADO') {
            throw new TareaExcMemeExceptioneption("Error en campo estado meme del meme");
        }
        $this->_estado_meme = $estado_meme;
    }

    public function setRutaImagenMeme($ruta_imagen_meme) {
        if ($ruta_imagen_meme === null || strlen($ruta_imagen_meme) > 1024 || strlen($ruta_imagen_meme) < 1) {
            throw new MemeException("Error en título del meme");
        }
        $this->_ruta_imagen_meme = $ruta_imagen_meme;
    }

    public function setTitulo($titulo) {
        if ($titulo === null || strlen($titulo) > 100 || strlen($titulo) < 1) {
            throw new MemeException("Error en título del meme");
        }
        $this->_titulo = $titulo;
    }

    // Puede ser null
    public function setTextoSuperior($texto_superior) {
        if ($texto_superior !== null && strlen($texto_superior) > 40) {
            throw new MemeException("Error en el texto superior del meme");
        }
        $this->_texto_superior = $texto_superior;
    }

    // Puede ser null
    public function setTextoInferior($texto_inferior) {
        if ($texto_inferior !== null && strlen($texto_inferior) > 40) {
            throw new MemeException("Error en el texto inferior del meme");
        }
        $this->_texto_inferior = $texto_inferior;
    }

    public function setFechaCreacion($fecha_creacion) {
        if ($fecha_creacion === null || 
        date_format(date_create_from_format('Y-m-d H:i', $fecha_creacion), 'Y-m-d H:i') !== $fecha_creacion) {
            throw new MemeException("Error en fecha de creación del meme");
        }
        $this->_fecha_creacion = $fecha_creacion;
    }

    // Puede ser null
    public function setFechaPublicacion($fecha_publicacion) {
        if ($fecha_publicacion !== null && 
        date_format(date_create_from_format('Y-m-d H:i', $fecha_publicacion), 'Y-m-d H:i') !== $fecha_publicacion) {
            throw new MemeException("Error en fecha la fecha de publicación  ");
        }
        $this->_fecha_publicacion = $fecha_publicacion;
    }

    public function getArray() {
        $meme = array();

        $meme['id'] = $this->getID();
        $meme['usuario_id'] = $this->getUsuarioID();
        $meme['likes'] = $this->getLikes();
        $meme['dislikes'] = $this->getDislikes();
        $meme['estado_meme'] = $this->getEstadoMeme();
        $meme['ruta_imagen_meme'] = $this->getRutaImagenMeme();
        $meme['titulo'] = $this->getTitulo();
        $meme['texto_superior'] = $this->getTextoSuperior();
        $meme['texto_inferior'] = $this->getTextoInferior();
        $meme['fecha_creacion'] = $this->getFechaCreacion();
        $meme['fecha_publicacion'] = $this->getFechaPublicacion();

        return $meme;
    }
}


?>