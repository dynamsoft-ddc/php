<?php
class FormData{
    private $_listFormData;
    
    function __construct(){
        $this->_listFormData = array();
    }
    
    function append($strKey, $value, $strFileName = NULL){
        array_push($this->_listFormData, array($strKey, $value, $strFileName));
    }
    
    function clear(){
        unset($this->_listFormData);
        $this->_listFormData = array();
    }
    
    function isValid(){
        return !is_null($this->_listFormData);
    }
    
    function getAll(){
        return $this->_listFormData;
    }
}
?>