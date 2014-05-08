<?php

class Store_Product {

    protected $_id = null;
    protected $_name = null;
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
    protected $_enableDiscount = 0;
    protected $_alias = null;
    protected $_calendario = null;

    public function __construct($id, $nombre, $description, $price) {
        $this->_id = $id;
        $this->_name = $nombre;
        $this->_description = $description;
        $this->_price = $price;
    }

    public function getId() {
        return $this->_id;
    }

    public function setId($value) {
        $this->_id = (int) $value;
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

    public function getName() {
        return $this->_name;
    }

    public function setName($value) {
        $this->_name = $value;
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
    
    public function getEnableDiscount() {
        return $this->_enableDiscount;
    }

    public function setEnableDiscount($value) {
        $this->_enableDiscount = $value;
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
}
