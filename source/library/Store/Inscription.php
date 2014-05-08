<?php

class Store_Inscription {

    protected $_codemp = null;
    protected $_nombre = null;
    protected $_description = null;
    protected $_detail = null;
    protected $_quantity = null;
    protected $_price = null;
    protected $_weight = null;
    protected $_boxDescription = '';
    protected $_urlPicture = '';
    protected $_origen = null;
    protected $_slug = null;
    protected $_points = 0;
    protected $_alias = null;
    protected $_calendario = null;
    
    protected $_talla = null;
    protected $_peso = null;
    protected $_muneca = null;
    protected $_indicegrasa = null;
    protected $_masamuscular = null;
    protected $_cintura = null;
    protected $_cadera = null;
    protected $_espalda = null;
    protected $_pecho = null;
    

    public function __construct($codemp) {
        $this->_codemp = $codemp;
//        $this->_name = $nombre;
//        $this->_description = $description;
//        $this->_price = $price;
    }

    public function getCodEmp() {
        return $this->_codemp;
    }

    public function setCodEmp($value) {
        $this->_codemp = (int) $value;
    }

    public function setSlug($value) {
        $this->_slug = $value;
    }

    public function getSlug() {
        return $this->_slug;
    }
    
    public function setPoints($value) {
        $this->_points = $value;
    }

    public function getPoints() {
        return $this->_points;
    }


    public function setAlias($value) {
        $this->_alias = $value;
    }

    public function getAlias() {
        return $this->_alias;
    }

    public function setCalendario($value) {
        $this->_calendario = $value;
    }

    public function getCalendario() {
        return $this->_calendario;
    }

    public function getNombre() {
        return $this->_nombre;
    }

    public function setNombre($value) {
        $this->_nombre = $value;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setDescription($value) {
        $this->_description = $value;
    }

    public function getDetail() {
        return $this->_detail;
    }

    public function setDetail($value) {
        $this->_detail = $value;
    }

    public function getQuantity() {
        return $this->_quantity;
    }

    public function setQuantity($value) {
        $this->_quantity = (int) $value;
    }

    public function getPrice() {
        return $this->_price;
    }

    public function setPrice($value) {
        $this->_price = (double) $value;
    }

    public function getDateAdded() {
        return $this->_dateAdded;
    }

    public function setDateAdded($value) {
        $this->_dateAdded = $value;
    }

    public function getWeight() {
        return $this->_weight;
    }

    public function setWeight($value) {
        $this->_weight = (double) $value;
    }
    
    public function getBoxDescription() {
        return $this->_boxDescription;
    }

    public function setBoxDescription($value) {
        $this->_boxDescription = $value;
    }
    
    public function getUrlPicture() {
        return $this->_urlPicture;
    }

    public function setUrlPicture($value) {
        $this->_urlPicture = $value;
    }
    
    public function getTalla() {
        return $this->_talla;
    }
    
    public function setTalla($value) {
        $this->_talla = $value;
    }
    
    public function getPeso() {
        return $this->_peso;
    }
    
    public function setPeso($value) {
        $this->_peso = $value;
    }
    
    public function getMuneca() {
        return $this->_muneca;
    }
    
    public function setMuneca($value) {
        $this->_muneca = $value;
    }
    
    public function getIndiceGrasa() {
        return $this->_indicegrasa;
    }
    
    public function setIndiceGrasa($value) {
        $this->_indicegrasa = $value;
    }
    
    public function getMasaMuscular() {
        return $this->_masamuscular;
    }
    
    public function setMasaMuscular($value) {
        $this->_masamuscular = $value;
    }
    
    public function getCintura() {
        return $this->_cintura;
    }
    
    public function setCintura($value) {
        $this->_cintura = $value;
    }
    
    public function getCadera() {
        return $this->_cadera;
    }
    
    public function setCadera($value) {
        $this->_cadera = $value;
    }
    
    public function getPecho() {
        return $this->_pecho;
    }
    
    public function setPecho($value) {
        $this->_pecho = $value;
    }
    
}
