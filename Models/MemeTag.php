<?php
    class MemeTagException extends Exception {}

    class MemeTag {
        private $_meme_id;
        private $_tag_id;

        public function __construct($meme_id, $tag_id){
            $this->setMemeID($meme_id);
            $this->setTagID($tag_id);
        }

        public function getMemeID() {
            return $this->_meme_id;
        }
    
        public function getTagID() {
            return $this->_tag_id;
        }

        public function setMemeID($meme_id) {
            if ($meme_id === null || !is_numeric($meme_id) || $meme_id <= 0 || $meme_id >= 2147483647) {
                throw new MemeTagException("Error en ID de meme del meme-tag");
            }
            $this->_meme_id = $meme_id;
        }

        public function setTagID($tag_id) {
            if ($tag_id === null || !is_numeric($tag_id) || $tag_id <= 0 || $tag_id >= 2147483647) {
                throw new MemeTagException("Error en ID de tag del meme-tag");
            }
            $this->_tag_id = $tag_id;
        }

        public function getArray() {
            $meme_tag = array();
    
            $meme_tag['meme_id'] = $this->getMemeID();
            $meme_tag['tag_id'] = $this->getTagID();
    
            return $meme_tag;
        }
    }
?>